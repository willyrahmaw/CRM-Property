<?php

namespace Tests\Feature;

use App\Enums\CommissionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\PaymentService;
use Tests\TestCase;

class FinanceAndCommissionTest extends TestCase
{
    public function test_payment_recording_and_finance_verification(): void
    {
        $paymentService = app(PaymentService::class);
        $booking = Booking::first();
        $finance = User::where('email', 'finance@propflow.local')->first();

        // Record a payment
        $payment = $paymentService->recordPayment(
            booking: $booking,
            type: PaymentType::INSTALLMENT,
            amount: 25000000,
            paymentDate: now()->toDateString(),
            paymentMethod: 'Transfer Bank Mandiri',
            referenceNumber: 'TRX-TEST-001'
        );

        $this->assertEquals(PaymentStatus::PENDING, $payment->status);

        // Finance verifies payment
        $paymentService->verifyPayment($payment, $finance);

        $this->assertEquals(PaymentStatus::VERIFIED, $payment->fresh()->status);
        $this->assertEquals($finance->id, $payment->fresh()->verified_by_id);
    }

    public function test_commission_calculation_and_multi_tier_distribution(): void
    {
        $commissionService = app(CommissionService::class);
        $booking = Booking::first();
        $finance = User::where('email', 'finance@propflow.local')->first();

        // Calculate commissions: 2.5% on unit price, 60% sales, 20% TL
        $commissions = $commissionService->calculateAndGenerate($booking);

        $this->assertCount(2, $commissions);

        $salesComm = $commissions->firstWhere('beneficiary_type', 'sales');
        $this->assertNotNull($salesComm);
        $this->assertEquals(CommissionStatus::PENDING, $salesComm->status);

        // Approve and pay
        $commissionService->approve($salesComm, $finance);
        $this->assertEquals(CommissionStatus::APPROVED, $salesComm->fresh()->status);

        $commissionService->markAsPaid($salesComm, $finance);
        $this->assertEquals(CommissionStatus::PAID, $salesComm->fresh()->status);
    }

    public function test_booking_approval_auto_generates_commissions(): void
    {
        $booking = Booking::create([
            'company_id' => Booking::first()->company_id,
            'customer_id' => Booking::first()->customer_id,
            'property_unit_id' => Booking::first()->property_unit_id,
            'sales_id' => Booking::first()->sales_id,
            'booking_number' => 'BKG-TEST-AUTO-01',
            'booking_date' => now()->toDateString(),
            'booking_fee' => 10000000,
            'unit_price' => 1000000000,
            'discount_amount' => 0,
            'final_price' => 1000000000,
            'payment_scheme' => \App\Enums\PaymentScheme::CASH_KERAS,
            'status' => \App\Enums\BookingStatus::PENDING,
        ]);

        $finance = User::where('email', 'finance@propflow.local')->first();
        $bookingService = app(\App\Services\BookingService::class);

        $this->assertEquals(0, $booking->commissions()->count());

        // Approving booking auto-generates commissions
        $bookingService->approveBooking($booking, $finance);

        $this->assertEquals(2, $booking->fresh()->commissions()->count());
        $this->assertDatabaseHas('commissions', [
            'booking_id' => $booking->id,
            'status' => CommissionStatus::PENDING->value,
            'beneficiary_type' => 'sales',
        ]);
    }

    public function test_finance_can_generate_commission_via_route(): void
    {
        $booking = Booking::first();
        $finance = User::where('email', 'finance@propflow.local')->first();

        $response = $this->actingAs($finance)->post(route('finance.commissions.generate', $booking), [
            'rate' => 3.0,
            'sales_share' => 70,
            'team_leader_share' => 15,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $salesComm = $booking->fresh()->commissions()->where('beneficiary_type', 'sales')->first();
        $this->assertNotNull($salesComm);
        $this->assertEquals(CommissionStatus::PENDING, $salesComm->status);
    }
}

