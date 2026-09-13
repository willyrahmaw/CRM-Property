<?php

namespace Tests\Feature;

use App\Enums\PaymentScheme;
use App\Enums\PropertyUnitStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Customer;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Customer $customer;
    private PropertyUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::create([
            'name' => 'PT Diamond Real Estate',
            'code' => 'DRE-CORP',
            'email' => 'corporate@diamond.local',
            'phone' => '021-77889900',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'company_id' => $company->id,
            'name' => 'Sales Agent 1',
            'email' => 'sales1@diamond.local',
            'phone' => '081234567890',
            'password' => bcrypt('password123'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'company_id' => $company->id,
            'name' => 'Budi Santoso',
            'phone' => '081122334455',
            'email' => 'budi@example.com',
        ]);

        $project = \App\Models\Project::create([
            'company_id' => $company->id,
            'name' => 'Grand Emerald',
            'slug' => 'grand-emerald',
            'code' => 'GEM',
        ]);

        $cluster = \App\Models\Cluster::create([
            'project_id' => $project->id,
            'name' => 'Sapphire',
            'code' => 'SPH',
        ]);

        $type = \App\Models\PropertyType::create([
            'project_id' => $project->id,
            'cluster_id' => $cluster->id,
            'name' => 'Type 70',
            'code' => 'T70',
        ]);

        $this->unit = PropertyUnit::create([
            'cluster_id' => $cluster->id,
            'property_type_id' => $type->id,
            'unit_number' => 'A-01',
            'land_area' => 120,
            'building_area' => 70,
            'base_price' => 1200000000,
            'selling_price' => 1500000000,
            'status' => PropertyUnitStatus::AVAILABLE,
        ]);
    }

    public function test_booking_with_existing_customer_id_and_empty_new_customer_inputs(): void
    {
        $payload = [
            'property_unit_id' => $this->unit->id,
            'customer_id' => $this->customer->id,
            'customer_name' => '',
            'customer_phone' => '',
            'customer_nik' => '',
            'customer_email' => '',
            'booking_fee' => 10000000,
            'payment_scheme' => PaymentScheme::CASH_KERAS->value,
            'discount_amount' => 0,
        ];

        $response = $this->actingAs($this->user)->post(route('sales.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'property_unit_id' => $this->unit->id,
        ]);
    }

    public function test_booking_with_existing_lead_id_and_empty_new_customer_inputs(): void
    {
        $lead = \App\Models\Lead::create([
            'company_id' => $this->user->company_id,
            'assigned_sales_id' => $this->user->id,
            'code' => 'LD-TEST-001',
            'name' => 'Calon Pembeli Dari Lead',
            'phone' => '081399887766',
            'email' => 'prospek@example.com',
            'source' => \App\Enums\LeadSource::WEBSITE,
            'status' => \App\Enums\LeadStatus::NEGOTIATION,
        ]);

        $payload = [
            'property_unit_id' => $this->unit->id,
            'lead_id' => $lead->id,
            'customer_id' => '',
            'customer_name' => '',
            'customer_phone' => '',
            'booking_fee' => 10000000,
            'payment_scheme' => PaymentScheme::KPR->value,
            'discount_amount' => 0,
        ];

        $response = $this->actingAs($this->user)->post(route('sales.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'lead_id' => $lead->id,
            'name' => 'Calon Pembeli Dari Lead',
            'phone' => '081399887766',
        ]);
    }

    public function test_booking_fails_when_no_customer_or_lead_and_inputs_are_empty(): void
    {
        $payload = [
            'property_unit_id' => $this->unit->id,
            'customer_id' => '',
            'lead_id' => '',
            'customer_name' => '',
            'customer_phone' => '',
            'booking_fee' => 10000000,
            'payment_scheme' => PaymentScheme::CASH_KERAS->value,
        ];

        $response = $this->actingAs($this->user)->post(route('sales.bookings.store'), $payload);

        $response->assertSessionHasErrors(['customer_name', 'customer_phone']);
    }

    public function test_booking_with_new_customer_inputs_creates_customer_and_booking(): void
    {
        $payload = [
            'property_unit_id' => $this->unit->id,
            'customer_id' => '',
            'lead_id' => '',
            'customer_name' => 'Siti Nurhaliza',
            'customer_phone' => '081299334455',
            'customer_email' => 'siti@example.com',
            'customer_nik' => '3201123456780001',
            'booking_fee' => 15000000,
            'payment_scheme' => PaymentScheme::CASH_BERTAHAP->value,
        ];

        $response = $this->actingAs($this->user)->post(route('sales.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'name' => 'Siti Nurhaliza',
            'phone' => '081299334455',
            'nik' => '3201123456780001',
        ]);
    }
}
