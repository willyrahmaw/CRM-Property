<?php

namespace Tests\Feature;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Models\Company;
use App\Services\LeadService;
use InvalidArgumentException;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    public function test_lead_creation_with_auto_assignment_and_scoring(): void
    {
        $leadService = app(LeadService::class);
        $company = Company::first();

        $lead = $leadService->create([
            'company_id' => $company->id,
            'name' => 'Test Lead Budi',
            'phone' => '081233221100',
            'email' => 'budi.test@example.com',
            'source' => LeadSource::FACEBOOK_ADS,
            'budget_min' => 700000000,
            'budget_max' => 950000000,
            'purchase_target_days' => 14,
        ]);

        $this->assertNotNull($lead->id);
        $this->assertStringStartsWith('LD-', $lead->code);
        $this->assertNotNull($lead->assigned_sales_id, 'Lead should be automatically assigned to an active sales agent');
        $this->assertGreaterThan(0, $lead->score);
    }

    public function test_marking_lead_as_lost_requires_reason(): void
    {
        $leadService = app(LeadService::class);
        $company = Company::first();

        $lead = $leadService->create([
            'company_id' => $company->id,
            'name' => 'Lead Gagal Test',
            'phone' => '081299887766',
            'source' => LeadSource::MANUAL,
        ]);

        $this->expectException(InvalidArgumentException::class);

        // Attempting to mark as LOST without reason must fail
        $leadService->updateStatus($lead, LeadStatus::LOST, null);
    }

    public function test_authorized_user_can_view_lead_detail_page(): void
    {
        $user = \App\Models\User::first();
        $lead = \App\Models\Lead::whereNotNull('interested_project_id')->first();

        $response = $this->actingAs($user)->get(route('crm.leads.show', $lead));

        $response->assertStatus(200);
        $response->assertSee($lead->name);
        $response->assertSee($lead->code);
    }
}

