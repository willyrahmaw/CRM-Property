<?php

namespace Tests\Feature;

use App\Enums\IndonesianTimezone;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndonesianTimezoneTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $owner;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Nusantara Property Dev',
            'code' => 'NPD-CORP',
            'email' => 'info@nusantara.local',
            'phone' => '021-55667788',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'company_id' => $this->company->id,
            'name' => 'Bapak Owner Nusantara',
            'email' => 'owner@nusantara.local',
            'phone' => '081122334455',
            'password' => bcrypt('CrmProperty123!'),
            'role' => UserRole::COMPANY_OWNER,
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'company_id' => $this->company->id,
            'name' => 'Agent Properti Nusantara',
            'email' => 'agent@nusantara.local',
            'phone' => '081234567890',
            'password' => bcrypt('CrmProperty123!'),
            'role' => UserRole::SALES_AGENT,
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);
    }

    public function test_user_defaults_to_wib_timezone(): void
    {
        $this->assertEquals('Asia/Jakarta', $this->company->timezone);
        $this->assertEquals(IndonesianTimezone::WIB, $this->user->getTimezoneEnum());
        $this->assertEquals('WIB', $this->user->getTimezoneCode());
    }

    public function test_owner_can_update_timezone_to_wita_in_profile_which_applies_to_entire_company(): void
    {
        $response = $this->actingAs($this->owner)->put(route('profile.update'), [
            'name' => 'Bapak Owner Nusantara',
            'email' => 'owner@nusantara.local',
            'timezone' => 'Asia/Makassar',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->company->refresh();
        $this->user->refresh();
        $this->owner->refresh();

        // When PT changes to WITA, PT, Owner, and all staff become WITA
        $this->assertEquals('Asia/Makassar', $this->company->timezone);
        $this->assertEquals(IndonesianTimezone::WITA, $this->company->getTimezoneEnum());
        $this->assertEquals(IndonesianTimezone::WITA, $this->owner->getTimezoneEnum());
        $this->assertEquals(IndonesianTimezone::WITA, $this->user->getTimezoneEnum());
        $this->assertEquals('WITA', $this->user->getTimezoneCode());
    }

    public function test_owner_can_update_timezone_to_wit_in_profile_which_applies_to_entire_company(): void
    {
        $response = $this->actingAs($this->owner)->put(route('profile.update'), [
            'name' => 'Bapak Owner Nusantara',
            'email' => 'owner@nusantara.local',
            'timezone' => 'Asia/Jayapura',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->company->refresh();
        $this->user->refresh();

        // When PT changes to WIT, everyone in the PT becomes WIT
        $this->assertEquals('Asia/Jayapura', $this->company->timezone);
        $this->assertEquals(IndonesianTimezone::WIT, $this->company->getTimezoneEnum());
        $this->assertEquals(IndonesianTimezone::WIT, $this->user->getTimezoneEnum());
        $this->assertEquals('WIT', $this->user->getTimezoneCode());
    }

    public function test_staff_cannot_override_company_pt_timezone(): void
    {
        // PT is WIB
        $this->assertEquals('Asia/Jakarta', $this->company->timezone);

        // Staff tries to update profile without being owner
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Agent Properti Nusantara',
            'email' => 'agent@nusantara.local',
            'timezone' => 'Asia/Makassar',
        ]);

        $response->assertRedirect(route('profile.edit'));

        $this->company->refresh();
        $this->user->refresh();

        // Company remains WIB, so staff timezone remains unified at WIB
        $this->assertEquals('Asia/Jakarta', $this->company->timezone);
        $this->assertEquals(IndonesianTimezone::WIB, $this->user->getTimezoneEnum());
        $this->assertEquals('WIB', $this->user->getTimezoneCode());
    }

    public function test_user_cannot_set_invalid_timezone(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Agent Properti London',
            'email' => 'agent@nusantara.local',
            'timezone' => 'Europe/London',
        ]);

        $response->assertSessionHasErrors('timezone');

        $this->user->refresh();
        $this->assertEquals('Asia/Jakarta', $this->user->timezone);
    }

    public function test_user_can_switch_timezone_via_quick_switcher(): void
    {
        $response = $this->actingAs($this->user)->post(route('timezone.switch'), [
            'timezone' => 'Asia/Makassar',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('Asia/Makassar', session('timezone'));

        $this->user->refresh();
        $this->assertEquals('Asia/Makassar', $this->user->timezone);
    }

    public function test_timezone_switcher_rejects_invalid_timezone(): void
    {
        $response = $this->actingAs($this->user)->post(route('timezone.switch'), [
            'timezone' => 'America/New_York',
        ]);

        $response->assertSessionHasErrors('timezone');
    }

    public function test_when_company_is_in_wib_all_users_in_company_use_wib(): void
    {
        $this->company->update(['timezone' => 'Asia/Jakarta']);

        $sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Staff',
            'email' => 'staff.sales@nusantara.local',
            'password' => bcrypt('CrmProperty123!'),
            'role' => UserRole::SALES_AGENT,
            'timezone' => 'Asia/Makassar', // even if user row had something else
        ]);

        // Company authoritative rule: PT in WIB => all users in WIB
        $this->assertEquals(IndonesianTimezone::WIB, $sales->getTimezoneEnum());
        $this->assertEquals('WIB', $sales->getTimezoneCode());
    }

    public function test_when_company_changes_to_wita_all_company_users_automatically_use_wita(): void
    {
        $this->company->update(['timezone' => 'Asia/Makassar']);

        $this->assertEquals(IndonesianTimezone::WITA, $this->user->getTimezoneEnum());
        $this->assertEquals('WITA', $this->user->getTimezoneCode());

        // Test middleware applies the company's WITA timezone
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();

        $this->assertEquals('Asia/Makassar', config('app.timezone'));
        $this->assertEquals('Asia/Makassar', date_default_timezone_get());
    }

    public function test_when_company_changes_to_wit_all_company_users_automatically_use_wit(): void
    {
        $this->company->update(['timezone' => 'Asia/Jayapura']);

        $this->assertEquals(IndonesianTimezone::WIT, $this->user->getTimezoneEnum());
        $this->assertEquals('WIT', $this->user->getTimezoneCode());

        // Test middleware applies the company's WIT timezone
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();

        $this->assertEquals('Asia/Jayapura', config('app.timezone'));
        $this->assertEquals('Asia/Jayapura', date_default_timezone_get());
    }

    public function test_timezone_switcher_updates_company_timezone_for_all_users(): void
    {
        $response = $this->actingAs($this->user)->post(route('timezone.switch'), [
            'timezone' => 'Asia/Makassar',
        ]);

        $response->assertRedirect();
        $this->company->refresh();
        $this->assertEquals('Asia/Makassar', $this->company->timezone);

        $this->user->refresh();
        $this->assertEquals(IndonesianTimezone::WITA, $this->user->getTimezoneEnum());
    }
}
