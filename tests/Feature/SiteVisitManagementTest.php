<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Enums\SiteVisitStatus;
use App\Enums\UserRole;
use App\Models\Cluster;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteVisitManagementTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $sales;
    private Project $project;
    private Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Harmoni Land SV Test',
            'code' => 'SV-TEST',
            'is_active' => true,
        ]);

        $this->sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales SV Tester',
            'email' => 'sales.sv@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Sentul SV',
            'slug' => 'grand-sentul-sv',
            'city' => 'Bogor',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $this->lead = Lead::create([
            'company_id' => $this->company->id,
            'code' => 'LD-SV-001',
            'name' => 'Bambang Konsumen SV',
            'phone' => '081299887711',
            'assigned_sales_id' => $this->sales->id,
            'status' => LeadStatus::QUALIFIED,
            'score' => 10,
        ]);
    }

    public function test_site_visits_index_is_accessible(): void
    {
        SiteVisit::create([
            'lead_id' => $this->lead->id,
            'user_id' => $this->sales->id,
            'project_id' => $this->project->id,
            'visit_date' => now()->addDay()->setTime(14, 0),
            'status' => SiteVisitStatus::SCHEDULED,
            'notes' => 'Survei rumah contoh tipe 45.',
        ]);

        $response = $this->actingAs($this->sales)->get(route('crm.site-visits.index'));

        $response->assertOk();
        $response->assertSee('Site Visits &amp; Survei Lokasi', false);
        $response->assertSee('Bambang Konsumen SV');
        $response->assertSee('Grand Sentul SV');
        $response->assertSee('Terjadwal');
    }

    public function test_sales_can_schedule_site_visit_and_advance_lead_stage(): void
    {
        $visitTime = now()->addDays(2)->setTime(10, 0)->format('Y-m-d\TH:i');

        $response = $this->actingAs($this->sales)->post(route('crm.site-visits.store'), [
            'lead_id' => $this->lead->id,
            'project_id' => $this->project->id,
            'visit_date' => $visitTime,
            'notes' => 'Membawa keluarga dan minat tipe hook.',
        ]);

        $response->assertRedirect(route('crm.site-visits.index'));

        $visit = SiteVisit::where('lead_id', $this->lead->id)->first();
        $this->assertNotNull($visit);
        $this->assertEquals(SiteVisitStatus::SCHEDULED, $visit->status);

        // Lead should advance to SITE_VISIT
        $this->assertEquals(LeadStatus::SITE_VISIT, $this->lead->fresh()->status);
    }

    public function test_completing_site_visit_increments_lead_score_and_logs_activity(): void
    {
        $visit = SiteVisit::create([
            'lead_id' => $this->lead->id,
            'user_id' => $this->sales->id,
            'project_id' => $this->project->id,
            'visit_date' => now()->subDay(),
            'status' => SiteVisitStatus::SCHEDULED,
        ]);

        $response = $this->actingAs($this->sales)->patch(route('crm.site-visits.update-status', $visit), [
            'status' => 'completed',
            'result' => 'Konsumen sangat antusias dan minta simulasi KPR BCA.',
        ]);

        $response->assertRedirect(route('crm.site-visits.index'));

        $this->assertEquals(SiteVisitStatus::COMPLETED, $visit->fresh()->status);
        // Score was 10, now +20 = 30
        $this->assertEquals(30, $this->lead->fresh()->score);
    }
}
