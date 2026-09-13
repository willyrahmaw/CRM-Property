<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $owner;
    protected User $salesManager;
    protected User $teamLeader;
    protected User $salesAgent;
    protected User $finance;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Harmoni Land Hierarchy Test',
            'code' => 'HIER-TEST',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'company_id' => null,
            'name' => 'Super Administrator',
            'email' => 'superadmin.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SUPER_ADMIN,
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'company_id' => $this->company->id,
            'name' => 'Bapak Owner Property',
            'email' => 'owner.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::COMPANY_OWNER,
            'is_active' => true,
        ]);

        $this->salesManager = User::create([
            'company_id' => $this->company->id,
            'name' => 'Manager Penjualan',
            'email' => 'manager.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_MANAGER,
            'is_active' => true,
        ]);

        $this->teamLeader = User::create([
            'company_id' => $this->company->id,
            'name' => 'Koordinator TL',
            'email' => 'tl.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::TEAM_LEADER,
            'is_active' => true,
        ]);

        $this->salesAgent = User::create([
            'company_id' => $this->company->id,
            'name' => 'Agen Sales Prospek',
            'email' => 'agent.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $this->finance = User::create([
            'company_id' => $this->company->id,
            'name' => 'Staff Finance Kasir',
            'email' => 'finance.hier@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::FINANCE,
            'is_active' => true,
        ]);
    }

    public function test_sales_manager_only_sees_sales_team_members_and_never_owner(): void
    {
        $response = $this->actingAs($this->salesManager)->get(route('settings.users.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Tim Sales');
        $response->assertSee('Koordinator TL');
        $response->assertSee('Agen Sales Prospek');

        // Company Owner and Finance MUST NOT be seen in Sales Team view
        $response->assertDontSee('Bapak Owner Property');
        $response->assertDontSee('Staff Finance Kasir');
    }

    public function test_sales_manager_cannot_deactivate_owner(): void
    {
        $this->assertTrue($this->owner->is_active);

        $response = $this->actingAs($this->salesManager)->patch(route('settings.users.toggle-status', $this->owner));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Owner status MUST remain active
        $this->assertTrue($this->owner->fresh()->is_active);
    }

    public function test_sales_manager_cannot_deactivate_super_admin_or_finance(): void
    {
        $responseAdmin = $this->actingAs($this->salesManager)->patch(route('settings.users.toggle-status', $this->superAdmin));
        $responseAdmin->assertSessionHas('error');
        $this->assertTrue($this->superAdmin->fresh()->is_active);

        $responseFinance = $this->actingAs($this->salesManager)->patch(route('settings.users.toggle-status', $this->finance));
        $responseFinance->assertSessionHas('error');
        $this->assertTrue($this->finance->fresh()->is_active);
    }

    public function test_sales_manager_can_deactivate_and_activate_sales_agent(): void
    {
        $this->assertTrue($this->salesAgent->is_active);

        // Deactivate agent
        $responseDeactivate = $this->actingAs($this->salesManager)->patch(route('settings.users.toggle-status', $this->salesAgent));
        $responseDeactivate->assertSessionHas('success');
        $this->assertFalse($this->salesAgent->fresh()->is_active);

        // Reactivate agent
        $responseActivate = $this->actingAs($this->salesManager)->patch(route('settings.users.toggle-status', $this->salesAgent));
        $responseActivate->assertSessionHas('success');
        $this->assertTrue($this->salesAgent->fresh()->is_active);
    }

    public function test_owner_sees_all_company_users(): void
    {
        $response = $this->actingAs($this->owner)->get(route('settings.users.index'));

        $response->assertOk();
        $response->assertSee('Pengguna & Struktur Peran');
        $response->assertSee('Bapak Owner Property');
        $response->assertSee('Manager Penjualan');
        $response->assertSee('Koordinator TL');
        $response->assertSee('Agen Sales Prospek');
        $response->assertSee('Staff Finance Kasir');
    }

    public function test_owner_can_deactivate_sales_manager(): void
    {
        $this->assertTrue($this->salesManager->is_active);

        $response = $this->actingAs($this->owner)->patch(route('settings.users.toggle-status', $this->salesManager));
        $response->assertSessionHas('success');
        $this->assertFalse($this->salesManager->fresh()->is_active);
    }

    public function test_owner_cannot_deactivate_self(): void
    {
        $response = $this->actingAs($this->owner)->patch(route('settings.users.toggle-status', $this->owner));
        $response->assertSessionHas('error');
        $this->assertTrue($this->owner->fresh()->is_active);
    }
}
