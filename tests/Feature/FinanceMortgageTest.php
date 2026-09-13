<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\MortgageStatus;
use App\Enums\PaymentScheme;
use App\Enums\PropertyUnitStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Mortgage;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceMortgageTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $finance;
    protected User $sales;
    protected Booking $booking;
    protected Mortgage $mortgage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Grand Harmony',
            'code' => 'GH-DEV',
            'email' => 'contact@grandharmony.com',
            'phone' => '021-88997766',
            'is_active' => true,
        ]);

        $this->finance = User::create([
            'company_id' => $this->company->id,
            'name' => 'Finance Staff',
            'email' => 'finance@test.com',
            'password' => bcrypt('Password123!'),
            'role' => UserRole::FINANCE,
            'is_active' => true,
        ]);

        $this->sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Agent',
            'email' => 'sales@test.com',
            'password' => bcrypt('Password123!'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Harmony Residence',
            'slug' => 'grand-harmony-residence',
            'city' => 'Tangerang',
            'status' => 'active',
        ]);

        $cluster = Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Beverly',
            'slug' => 'cluster-beverly',
        ]);

        $unit = PropertyUnit::create([
            'cluster_id' => $cluster->id,
            'unit_number' => 'B-01',
            'building_area' => 90,
            'land_area' => 120,
            'base_price' => 1000000000,
            'selling_price' => 1200000000,
            'status' => PropertyUnitStatus::BOOKED,
        ]);

        $customer = Customer::create([
            'company_id' => $this->company->id,
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);

        $this->booking = Booking::create([
            'company_id' => $this->company->id,
            'customer_id' => $customer->id,
            'property_unit_id' => $unit->id,
            'sales_id' => $this->sales->id,
            'booking_number' => 'BKG-2026-TEST',
            'booking_date' => now()->toDateString(),
            'booking_fee' => 10000000,
            'unit_price' => 1200000000,
            'discount_amount' => 0,
            'final_price' => 1200000000,
            'payment_scheme' => PaymentScheme::KPR,
            'status' => BookingStatus::APPROVED,
        ]);

        $this->mortgage = Mortgage::create([
            'booking_id' => $this->booking->id,
            'bank_name' => 'Bank Mandiri',
            'submission_amount' => 1000000000,
            'tenor_years' => 15,
            'interest_rate' => 6.5,
            'status' => MortgageStatus::SUBMITTED,
            'application_date' => now()->toDateString(),
        ]);
    }

    public function test_finance_user_can_access_mortgages_page(): void
    {
        $response = $this->actingAs($this->finance)->get(route('finance.mortgages.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen KPR & Akad Kredit');
        $response->assertSee('Bank Mandiri');
        $response->assertSee('Budi Santoso');
    }

    public function test_sales_agent_cannot_access_finance_mortgages_page(): void
    {
        $response = $this->actingAs($this->sales)->get(route('finance.mortgages.index'));

        $response->assertStatus(403);
    }

    public function test_finance_can_update_mortgage_to_sp3k_approved(): void
    {
        $response = $this->actingAs($this->finance)->patch(
            route('finance.mortgages.update', $this->mortgage),
            [
                'status' => MortgageStatus::APPROVED->value,
                'bank_name' => 'Bank Mandiri',
                'submission_amount' => 1000000000,
                'approved_amount' => 950000000,
                'tenor_years' => 15,
                'interest_rate' => 6.25,
                'sp3k_date' => now()->toDateString(),
                'notes' => 'SP3K resmi terbit dengan plafon 950jt',
            ]
        );

        $response->assertRedirect(route('finance.mortgages.index'));
        $this->assertDatabaseHas('mortgages', [
            'id' => $this->mortgage->id,
            'status' => MortgageStatus::APPROVED->value,
            'approved_amount' => 950000000,
        ]);
    }

    public function test_finance_can_update_mortgage_to_contract_signed(): void
    {
        $response = $this->actingAs($this->finance)->patch(
            route('finance.mortgages.update', $this->mortgage),
            [
                'status' => MortgageStatus::CONTRACT_SIGNED->value,
                'bank_name' => 'Bank Mandiri',
                'submission_amount' => 1000000000,
                'approved_amount' => 950000000,
                'tenor_years' => 15,
                'contract_date' => now()->toDateString(),
                'notes' => 'Akad kredit selesai di hadapan notaris',
            ]
        );

        $response->assertRedirect(route('finance.mortgages.index'));
        $this->assertDatabaseHas('mortgages', [
            'id' => $this->mortgage->id,
            'status' => MortgageStatus::CONTRACT_SIGNED->value,
        ]);
    }
}
