<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileLayoutResponsivenessTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Citra Grand Mahakarya',
            'code' => 'CGM',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'company_id' => $this->company->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SUPER_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_layout_renders_mobile_drawer_and_hamburger_button(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        // Cek elemen drawer navigasi mobile dan hamburger menu
        $response->assertSee('id="mobile-sidebar"', false);
        $response->assertSee('id="mobile-sidebar-backdrop"', false);
        $response->assertSee('data-mobile-menu-open', false);
        $response->assertSee('data-mobile-menu-close', false);
    }

    public function test_crm_leads_page_renders_with_mobile_friendly_layout(): void
    {
        $response = $this->actingAs($this->user)->get(route('crm.leads.index'));

        $response->assertOk();
        $response->assertSee('id="mobile-sidebar"', false);
        $response->assertSee('data-mobile-menu-open', false);
        $response->assertSee('prop-table', false);
    }

    public function test_mobile_drawer_contains_navigation_links(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('crm.leads.index'));
        $response->assertSee(route('inventory.projects.index'));
        $response->assertSee(route('sales.bookings.index'));
        $response->assertSee(route('finance.payments.index'));
    }
}
