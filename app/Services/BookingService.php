<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentScheme;
use App\Enums\PropertyUnitStatus;
use App\Exceptions\InvalidBookingStateException;
use App\Exceptions\PropertyUnitUnavailableException;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected CommissionService $commissionService
    ) {}

    /**
     * Generate unique sequential booking number.
     */
    public function generateBookingNumber(): string
    {
        $prefix = 'BKG-' . date('Ym') . '-';
        $latest = Booking::withoutGlobalScopes()
            ->where('booking_number', 'LIKE', $prefix . '%')
            ->orderBy('booking_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->booking_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Create a new property booking with pessimistic locking and concurrency defense.
     */
    public function createBooking(
        Customer $customer,
        string $unitId,
        User $sales,
        float $bookingFee,
        float $discountAmount = 0,
        PaymentScheme $paymentScheme = PaymentScheme::KPR,
        ?string $notes = null
    ): Booking {
        return DB::transaction(function () use (
            $customer,
            $unitId,
            $sales,
            $bookingFee,
            $discountAmount,
            $paymentScheme,
            $notes
        ) {
            // 1. Lock property unit row with pessimistic locking to eliminate race conditions
            $unit = PropertyUnit::query()
                ->whereKey($unitId)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Re-verify unit availability inside transaction boundary
            if (!$unit->isAvailable()) {
                throw new PropertyUnitUnavailableException(
                    "Unit {$unit->unit_number} (Blok {$unit->block}) sudah berstatus {$unit->status->label()} dan tidak dapat di-booking."
                );
            }

            // 3. Compute final pricing
            $unitPrice = (float) $unit->selling_price;
            $finalPrice = max(0, $unitPrice - $discountAmount);

            // 4. Create booking record
            $booking = Booking::create([
                'company_id' => $customer->company_id,
                'customer_id' => $customer->id,
                'property_unit_id' => $unit->id,
                'sales_id' => $sales->id,
                'booking_number' => $this->generateBookingNumber(),
                'booking_date' => now()->toDateString(),
                'booking_fee' => $bookingFee,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'payment_scheme' => $paymentScheme,
                'status' => BookingStatus::PENDING,
                'notes' => $notes,
            ]);

            // 5. Transition unit status to RESERVED while awaiting booking fee verification
            $unit->update([
                'status' => PropertyUnitStatus::RESERVED,
            ]);

            // 6. Audit Trail
            $this->auditLogService->log(
                action: 'booking_created',
                entity: $booking,
                before: null,
                after: [
                    'booking_number' => $booking->booking_number,
                    'unit_id' => $unit->id,
                    'unit_number' => $unit->unit_number,
                    'final_price' => $finalPrice,
                ]
            );

            return $booking;
        });
    }

    /**
     * Approve booking and formally mark unit as BOOKED.
     */
    public function approveBooking(Booking $booking, User $approver): Booking
    {
        return DB::transaction(function () use ($booking, $approver) {
            if ($booking->status !== BookingStatus::PENDING) {
                throw new InvalidBookingStateException('Hanya booking berstatus PENDING yang dapat disetujui.');
            }

            // Lock unit again
            $unit = PropertyUnit::query()
                ->whereKey($booking->property_unit_id)
                ->lockForUpdate()
                ->firstOrFail();

            $booking->update([
                'status' => BookingStatus::APPROVED,
                'approved_by_id' => $approver->id,
            ]);

            $unit->update([
                'status' => PropertyUnitStatus::BOOKED,
            ]);

            // Auto-generate commission for sales & team leader if enabled by company owner policy
            $autoGenerate = $booking->company?->getCommissionSetting('auto_generate_on_booking_fee', true) ?? true;
            if ($autoGenerate) {
                $this->commissionService->calculateAndGenerate($booking);
            }

            $this->auditLogService->log(
                action: 'booking_approved',
                entity: $booking,
                before: ['status' => BookingStatus::PENDING->value],
                after: ['status' => BookingStatus::APPROVED->value, 'approved_by' => $approver->name]
            );

            return $booking;
        });
    }

    /**
     * Cancel booking and release unit back to AVAILABLE.
     */
    public function cancelBooking(Booking $booking, string $reason, User $canceller): Booking
    {
        return DB::transaction(function () use ($booking, $reason, $canceller) {
            if (in_array($booking->status, [BookingStatus::CANCELLED, BookingStatus::REJECTED])) {
                throw new InvalidBookingStateException('Booking ini sudah dibatalkan atau ditolak sebelumnya.');
            }

            $oldStatus = $booking->status;

            // Lock unit
            $unit = PropertyUnit::query()
                ->whereKey($booking->property_unit_id)
                ->lockForUpdate()
                ->firstOrFail();

            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'cancellation_reason' => $reason,
            ]);

            // Release unit back to AVAILABLE
            $unit->update([
                'status' => PropertyUnitStatus::AVAILABLE,
            ]);

            $this->auditLogService->log(
                action: 'booking_cancelled',
                entity: $booking,
                before: ['status' => $oldStatus->value],
                after: [
                    'status' => BookingStatus::CANCELLED->value,
                    'reason' => $reason,
                    'cancelled_by' => $canceller->name,
                ]
            );

            return $booking;
        });
    }
}
