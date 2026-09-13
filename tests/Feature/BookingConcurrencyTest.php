<?php

namespace Tests\Feature;

use App\Enums\PaymentScheme;
use App\Enums\PropertyUnitStatus;
use App\Exceptions\PropertyUnitUnavailableException;
use App\Models\Customer;
use App\Models\PropertyUnit;
use App\Models\User;
use App\Services\BookingService;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    public function test_booking_locks_unit_and_prevents_duplicate_booking(): void
    {
        $bookingService = app(BookingService::class);

        $sales = User::where('email', 'andi@propflow.local')->first();
        $customer1 = Customer::first();
        $customer2 = Customer::skip(1)->first();

        // Get an available unit
        $unit = PropertyUnit::where('status', PropertyUnitStatus::AVAILABLE)->first();
        $this->assertNotNull($unit, 'Unit available must exist for testing');

        // First booking succeeds
        $booking1 = $bookingService->createBooking(
            customer: $customer1,
            unitId: $unit->id,
            sales: $sales,
            bookingFee: 10000000,
            discountAmount: 0,
            paymentScheme: PaymentScheme::KPR
        );

        $this->assertNotNull($booking1);
        $this->assertEquals(PropertyUnitStatus::RESERVED, $unit->fresh()->status);

        // Second booking attempt by customer 2 on the same unit must be prevented with PropertyUnitUnavailableException
        $this->expectException(PropertyUnitUnavailableException::class);

        $bookingService->createBooking(
            customer: $customer2,
            unitId: $unit->id,
            sales: $sales,
            bookingFee: 10000000,
            discountAmount: 0,
            paymentScheme: PaymentScheme::KPR
        );
    }
}
