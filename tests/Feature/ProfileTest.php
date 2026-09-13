<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Diamond Real Estate',
            'code' => 'DRE-CORP',
            'email' => 'corporate@diamond.local',
            'phone' => '021-77889900',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'company_id' => $this->company->id,
            'name' => 'Willy Property Specialist',
            'email' => 'willy@diamond.local',
            'phone' => '081234567890',
            'password' => bcrypt('password123'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Profil Pengguna &amp; Keamanan Akun', false);
        $response->assertSee('Willy Property Specialist');
        $response->assertSee('willy@diamond.local');
        $response->assertSee('PT Diamond Real Estate');
        $response->assertSee('Sales / Agent');
    }

    public function test_user_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Willy Senior Property Consultant',
            'email' => 'willy.consultant@diamond.local',
            'phone' => '081199001122',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account_number' => '8899001122',
            'bank_account_holder' => 'Willy Property Specialist',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Willy Senior Property Consultant', $this->user->name);
        $this->assertEquals('willy.consultant@diamond.local', $this->user->email);
        $this->assertEquals('081199001122', $this->user->phone);
        $this->assertEquals('Bank Central Asia (BCA)', $this->user->bank_name);
        $this->assertEquals('8899001122', $this->user->bank_account_number);
        $this->assertEquals('Willy Property Specialist', $this->user->bank_account_holder);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('willy_avatar.jpg', 300, 300);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Willy Property Specialist',
            'email' => 'willy@diamond.local',
            'phone' => '081234567890',
            'avatar' => $avatar,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->avatar_path);
        Storage::disk('public')->assertExists($this->user->avatar_path);
        $this->assertNotNull($this->user->avatar_url);
    }

    public function test_user_can_delete_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.png');
        $path = $avatar->store('avatars', 'public');
        $this->user->update(['avatar_path' => $path]);

        $this->assertNotNull($this->user->avatar_path);

        $response = $this->actingAs($this->user)->delete(route('profile.avatar.destroy'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNull($this->user->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => 'password123',
            'password' => 'newSecretPassword2026',
            'password_confirmation' => 'newSecretPassword2026',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newSecretPassword2026', $this->user->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => 'wrongOldPassword',
            'password' => 'newSecretPassword2026',
            'password_confirmation' => 'newSecretPassword2026',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->user->refresh();
        $this->assertFalse(Hash::check('newSecretPassword2026', $this->user->password));
        $this->assertTrue(Hash::check('password123', $this->user->password));
    }

    public function test_user_cannot_update_password_with_less_than_8_characters(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => 'password123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_update_password_with_mismatched_confirmation(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => 'password123',
            'password' => 'standardPassword123',
            'password_confirmation' => 'differentPassword123',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
