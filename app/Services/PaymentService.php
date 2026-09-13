<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected BookingService $bookingService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Generate unique payment voucher number.
     */
    public function generatePaymentNumber(): string
    {
        $prefix = 'PAY-' . date('Ym') . '-';
        $latest = Payment::withoutGlobalScopes()
            ->where('payment_number', 'LIKE', $prefix . '%')
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->payment_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Record a new customer payment with receipt proof upload.
     */
    public function recordPayment(
        Booking $booking,
        PaymentType $type,
        float $amount,
        string $paymentDate,
        string $paymentMethod = 'Transfer Bank',
        ?string $referenceNumber = null,
        ?UploadedFile $proofFile = null,
        ?string $notes = null
    ): Payment {
        return DB::transaction(function () use (
            $booking,
            $type,
            $amount,
            $paymentDate,
            $paymentMethod,
            $referenceNumber,
            $proofFile,
            $notes
        ) {
            $proofPath = null;
            if ($proofFile) {
                // Secure upload with randomized name
                $filename = (string) Str::uuid() . '.' . $proofFile->getClientOriginalExtension();
                $proofPath = $proofFile->storeAs('payments/receipts', $filename, 'public');
            }

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_number' => $this->generatePaymentNumber(),
                'payment_type' => $type,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'proof_path' => $proofPath,
                'status' => PaymentStatus::PENDING,
                'notes' => $notes,
            ]);

            $this->auditLogService->log(
                action: 'payment_recorded',
                entity: $payment,
                before: null,
                after: [
                    'payment_number' => $payment->payment_number,
                    'booking_id' => $booking->id,
                    'amount' => $amount,
                ]
            );

            return $payment;
        });
    }

    /**
     * Verify payment by authorized finance personnel.
     */
    public function verifyPayment(Payment $payment, User $verifier): Payment
    {
        return DB::transaction(function () use ($payment, $verifier) {
            $payment->update([
                'status' => PaymentStatus::VERIFIED,
                'verified_by_id' => $verifier->id,
                'verified_at' => now(),
            ]);

            // If this is the booking fee and booking is pending, auto-approve booking
            $booking = $payment->booking;
            if ($payment->payment_type === PaymentType::BOOKING_FEE && $booking->status === BookingStatus::PENDING) {
                $this->bookingService->approveBooking($booking, $verifier);
            }

            $this->auditLogService->log(
                action: 'payment_verified',
                entity: $payment,
                before: ['status' => PaymentStatus::PENDING->value],
                after: [
                    'status' => PaymentStatus::VERIFIED->value,
                    'verified_by' => $verifier->name,
                ]
            );

            return $payment;
        });
    }
}
