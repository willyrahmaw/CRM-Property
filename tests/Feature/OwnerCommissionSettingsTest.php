<?php

namespace Tests\Feature;

use App\Enums\PaymentScheme;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Customer;
use App\Models\PropertyUnit;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerCommissionSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $owner;
    protected User $sales;
    protected User $finance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Harmoni Land Owner Test',
            'code' => 'OWN-COMM',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'company_id' => $this->company->id,
            'name' => 'Owner Property',
            'email' => 'owner.comm@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::COMPANY_OWNER,
            'is_active' => true,
        ]);

        $this->sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Agent',
            'email' => 'sales.comm@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $this->finance = User::create([
            'company_id' => $this->company->id,
            'name' => 'Finance Staff',
            'email' => 'finance.comm@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::FINANCE,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_commission_settings(): void
    {
        $response = $this->get(route('settings.commissions.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sales_and_finance_cannot_access_commission_settings(): void
    {
        $salesResponse = $this->actingAs($this->sales)->get(route('settings.commissions.index'));
        $salesResponse->assertForbidden();

        $financeResponse = $this->actingAs($this->finance)->get(route('settings.commissions.index'));
        $financeResponse->assertForbidden();
    }

    public function test_owner_can_view_commission_settings_page(): void
    {
        $response = $this->actingAs($this->owner)->get(route('settings.commissions.index'));
        $response->assertOk();
        $response->assertSee('Pengaturan Skema & Kebijakan Komisi');
        $response->assertSee('Total Tarif Komisi Penjualan');
    }

    public function test_owner_can_update_commission_settings_successfully(): void
    {
        $payload = [
            'total_rate' => 3.5,
            'sales_share' => 70.0,
            'team_leader_share' => 15.0,
            'agency_share' => 15.0,
            'auto_generate_on_booking_fee' => '1',
            'disbursement_policy' => 'after_dp_paid',
            'terms_and_conditions' => 'KTP & NPWP wajib terverifikasi HRD.',
        ];

        $response = $this->actingAs($this->owner)->put(route('settings.commissions.update'), $payload);

        $response->assertRedirect(route('settings.commissions.index'));
        $response->assertSessionHas('success');

        $this->company->refresh();
        $this->assertEquals(3.5, $this->company->getCommissionSetting('total_rate'));
        $this->assertEquals(70.0, $this->company->getCommissionSetting('sales_share'));
        $this->assertEquals(15.0, $this->company->getCommissionSetting('team_leader_share'));
        $this->assertEquals(15.0, $this->company->getCommissionSetting('agency_share'));
        $this->assertEquals('after_dp_paid', $this->company->getCommissionSetting('disbursement_policy'));
    }

    public function test_commission_settings_update_fails_if_shares_do_not_sum_to_100(): void
    {
        $payload = [
            'total_rate' => 3.0,
            'sales_share' => 60.0,
            'team_leader_share' => 20.0,
            'agency_share' => 10.0, // total = 90%
            'disbursement_policy' => 'after_booking_fee',
        ];

        $response = $this->actingAs($this->owner)->put(route('settings.commissions.update'), $payload);

        $response->assertSessionHasErrors(['shares_total']);
    }

    public function test_custom_owner_commission_settings_are_applied_to_new_bookings(): void
    {
        // Owner sets 3% total commission, 70% sales, 20% TL, 10% agency
        $this->company->commission_settings = [
            'total_rate' => 3.0,
            'sales_share' => 70.0,
            'team_leader_share' => 20.0,
            'agency_share' => 10.0,
            'auto_generate_on_booking_fee' => true,
        ];
        $this->company->save();

        $customer = Customer::create([
            'company_id' => $this->company->id,
            'name' => 'Konsumen Buyer Unit',
            'phone' => '081211112222',
        ]);

        $project = \App\Models\Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Emerald City',
            'slug' => 'grand-emerald-city',
            'status' => \App\Enums\ProjectStatus::ACTIVE,
        ]);

        $cluster = \App\Models\Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Ruby',
            'code' => 'RBY',
        ]);

        $unit = PropertyUnit::create([
            'cluster_id' => $cluster->id,
            'unit_number' => 'R-01',
            'block' => 'A',
            'building_area' => 100,
            'land_area' => 120,
            'floors' => 2,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'base_price' => 1000000000,
            'selling_price' => 1000000000,
            'status' => \App\Enums\PropertyUnitStatus::AVAILABLE,
        ]);

        $booking = Booking::create([
            'company_id' => $this->company->id,
            'customer_id' => $customer->id,
            'property_unit_id' => $unit->id,
            'sales_id' => $this->sales->id,
            'booking_number' => 'BKG-OWN-TEST-01',
            'booking_date' => now()->toDateString(),
            'booking_fee' => 10000000,
            'unit_price' => 1000000000,
            'discount_amount' => 0,
            'final_price' => 1000000000,
            'payment_scheme' => PaymentScheme::CASH_KERAS,
            'status' => \App\Enums\BookingStatus::PENDING,
        ]);

        // Approve booking -> triggers auto-generate using Owner's settings
        app(BookingService::class)->approveBooking($booking, $this->owner);

        $salesComm = $booking->fresh()->commissions()->where('beneficiary_type', 'sales')->first();
        $this->assertNotNull($salesComm);

        // 3.0% of 1.000.000.000 = 30.000.000
        // Sales share 70% = 21.000.000 (2.10% effective rate)
        $this->assertEquals(21000000, (float) $salesComm->amount);
        $this->assertEquals(2.10, (float) $salesComm->percentage);
    }
}
