<?php

namespace Tests\Feature;

use App\Enums\NegotiationApprovalStatus;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Cluster;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Negotiation;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegotiationAndDiscountTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $manager;
    private User $sales;
    private Lead $lead;
    private PropertyUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Harmoni Land Test',
            'code' => 'HL-TEST',
            'is_active' => true,
        ]);

        $this->manager = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Manager Budi',
            'email' => 'manager@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_MANAGER,
            'is_active' => true,
        ]);

        $this->sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Agent Rian',
            'email' => 'sales@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Harmony City',
            'slug' => 'harmony-city',
            'city' => 'Tangerang',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $cluster = Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Jasmine',
            'code' => 'JAS',
        ]);

        $this->unit = PropertyUnit::create([
            'cluster_id' => $cluster->id,
            'unit_number' => 'JAS-01',
            'block' => 'A',
            'land_area' => 120,
            'building_area' => 80,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floors' => 2,
            'base_price' => 1000000000,
            'selling_price' => 1000000000,
        ]);

        $this->lead = Lead::create([
            'company_id' => $this->company->id,
            'code' => 'LD-TEST-001',
            'name' => 'Calon Pembeli Prospek',
            'phone' => '08123456789',
            'assigned_sales_id' => $this->sales->id,
        ]);
    }

    public function test_negotiations_page_is_accessible(): void
    {
        $negotiation = Negotiation::create([
            'lead_id' => $this->lead->id,
            'property_unit_id' => $this->unit->id,
            'sales_id' => $this->sales->id,
            'initial_price' => 1000000000,
            'customer_offer_price' => 970000000,
            'final_price' => 970000000,
            'discount_amount' => 30000000,
            'discount_percentage' => 3.0,
            'promo_description' => 'Promo Diskon Konsumen',
            'approval_status' => NegotiationApprovalStatus::PENDING,
        ]);

        $response = $this->actingAs($this->sales)->get(route('sales.negotiations.index'));

        $response->assertOk();
        $response->assertSee('Negosiasi &amp; Persetujuan Diskon', false);
        $response->assertSee('Calon Pembeli Prospek');
        $response->assertSee('JAS-01');
        $response->assertSee('Menunggu Approval');
    }

    public function test_sales_can_submit_negotiation_request(): void
    {
        $response = $this->actingAs($this->sales)->post(route('sales.negotiations.store'), [
            'lead_id' => $this->lead->id,
            'property_unit_id' => $this->unit->id,
            'customer_offer_price' => 950000000,
            'promo_description' => 'Subsidi DP 50 Juta',
            'notes' => 'Konsumen siap closing cepat.',
        ]);

        $negotiation = Negotiation::where('lead_id', $this->lead->id)->first();
        $this->assertNotNull($negotiation);
        $this->assertEquals(50000000, $negotiation->discount_amount);
        $this->assertEquals(NegotiationApprovalStatus::PENDING, $negotiation->approval_status);

        $response->assertRedirect(route('sales.negotiations.show', $negotiation));
    }

    public function test_manager_can_approve_negotiation(): void
    {
        $negotiation = Negotiation::create([
            'lead_id' => $this->lead->id,
            'property_unit_id' => $this->unit->id,
            'sales_id' => $this->sales->id,
            'initial_price' => 1000000000,
            'customer_offer_price' => 960000000,
            'final_price' => 960000000,
            'discount_amount' => 40000000,
            'discount_percentage' => 4.0,
            'approval_status' => NegotiationApprovalStatus::PENDING,
        ]);

        $response = $this->actingAs($this->manager)->post(route('sales.negotiations.approve', $negotiation), [
            'notes' => 'Disetujui untuk closing akhir bulan.',
        ]);

        $response->assertRedirect(route('sales.negotiations.show', $negotiation));
        $this->assertEquals(NegotiationApprovalStatus::APPROVED, $negotiation->fresh()->approval_status);
        $this->assertEquals($this->manager->id, $negotiation->fresh()->approved_by_id);
    }

    public function test_non_manager_cannot_approve_negotiation(): void
    {
        $negotiation = Negotiation::create([
            'lead_id' => $this->lead->id,
            'property_unit_id' => $this->unit->id,
            'sales_id' => $this->sales->id,
            'initial_price' => 1000000000,
            'customer_offer_price' => 960000000,
            'final_price' => 960000000,
            'discount_amount' => 40000000,
            'discount_percentage' => 4.0,
            'approval_status' => NegotiationApprovalStatus::PENDING,
        ]);

        $response = $this->actingAs($this->sales)->post(route('sales.negotiations.approve', $negotiation));

        $response->assertForbidden();
        $this->assertEquals(NegotiationApprovalStatus::PENDING, $negotiation->fresh()->approval_status);
    }

    public function test_manager_can_reject_negotiation(): void
    {
        $negotiation = Negotiation::create([
            'lead_id' => $this->lead->id,
            'property_unit_id' => $this->unit->id,
            'sales_id' => $this->sales->id,
            'initial_price' => 1000000000,
            'customer_offer_price' => 800000000,
            'final_price' => 800000000,
            'discount_amount' => 200000000,
            'discount_percentage' => 20.0,
            'approval_status' => NegotiationApprovalStatus::PENDING,
        ]);

        $response = $this->actingAs($this->manager)->post(route('sales.negotiations.reject', $negotiation), [
            'reason' => 'Diskon 20% melebihi batas margin operasional developer.',
        ]);

        $response->assertRedirect(route('sales.negotiations.show', $negotiation));
        $this->assertEquals(NegotiationApprovalStatus::REJECTED, $negotiation->fresh()->approval_status);
    }
}
