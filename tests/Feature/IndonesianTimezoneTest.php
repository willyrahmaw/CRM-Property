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
        $this->assertEquals('Asia/Jakarta', $this->user->timezone);
        $this->assertEquals(IndonesianTimezone::WIB, $this->user->getTimezoneEnum());
        $this->assertEquals('WIB', $this->user->getTimezoneCode());
    }

    public function test_user_can_update_timezone_to_wita_in_profile(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Agent Properti Bali',
            'email' => 'agent@nusantara.local',
            'timezone' => 'Asia/Makassar',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Asia/Makassar', $this->user->timezone);
        $this->assertEquals(IndonesianTimezone::WITA, $this->user->getTimezoneEnum());
        $this->assertEquals('WITA', $this->user->getTimezoneCode());
    }

    public function test_user_can_update_timezone_to_wit_in_profile(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Agent Properti Papua',
            'email' => 'agent@nusantara.local',
            'timezone' => 'Asia/Jayapura',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Asia/Jayapura', $this->user->timezone);
        $this->assertEquals(IndonesianTimezone::WIT, $this->user->getTimezoneEnum());
        $this->assertEquals('WIT', $this->user->getTimezoneCode());
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

    public function test_middleware_applies_user_timezone(): void
    {
        $this->user->update(['timezone' => 'Asia/Jayapura']);

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();

        $this->assertEquals('Asia/Jayapura', config('app.timezone'));
        $this->assertEquals('Asia/Jayapura', date_default_timezone_get());
    }
}
