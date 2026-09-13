<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $owner;
    private User $sales;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Royal Emerald Developer',
            'code' => 'RED-DEV',
            'email' => 'info@emerald.local',
            'phone' => '021-99887766',
            'address' => 'Emerald Headquarter Lt. 5, Jakarta Selatan',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'company_id' => $this->company->id,
            'name' => 'Bapak Owner',
            'email' => 'owner@emerald.local',
            'password' => bcrypt('password'),
            'role' => UserRole::COMPANY_OWNER,
            'is_active' => true,
        ]);

        $this->sales = User::create([
            'company_id' => $this->company->id,
            'name' => 'Agent Sales',
            'email' => 'sales@emerald.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);
    }

    public function test_owner_can_access_website_settings_page(): void
    {
        $response = $this->actingAs($this->owner)->get(route('settings.website.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Website');
        $response->assertSee('PT Royal Emerald Developer');
        $response->assertSee('Akses Eksklusif Owner');
    }

    public function test_owner_can_update_website_settings(): void
    {
        Storage::fake('public');

        $fakeLogo = UploadedFile::fake()->image('company_logo.png', 400, 100);

        $payload = [
            'site_title' => 'Royal Emerald Luxury Portal',
            'tagline' => 'Hunian Prestisius Bernilai Investasi Tinggi',
            'about_text' => 'Developer properti terdepan di Indonesia.',
            'email' => 'corporate@emerald.local',
            'phone' => '021-55443322',
            'whatsapp' => '081299887766',
            'address' => 'Jl. Jenderal Sudirman No. 100, Jakarta Selatan',
            'operational_hours' => 'Setiap Hari 09:00 - 19:00 WIB',
            'announcement_active' => '1',
            'announcement_text' => 'Subsidi DP 10% & Bebas Semua Biaya Notaris!',
            'instagram' => '@royalemerald.official',
            'facebook' => 'https://facebook.com/royalemerald',
            'youtube' => 'https://youtube.com/@royalemerald',
            'tiktok' => '@royalemerald',
            'meta_title' => 'Royal Emerald — Perumahan Mewah Jakarta Selatan',
            'meta_description' => 'Katalog kaveling & rumah modern mewah dengan fasilitas lengkap.',
            'meta_keywords' => 'perumahan mewah jakarta, kpr murah',
            'google_analytics_id' => 'G-EMERALD01',
            'facebook_pixel_id' => '1234567890',
            'logo' => $fakeLogo,
        ];

        $response = $this->actingAs($this->owner)->put(route('settings.website.update'), $payload);

        $response->assertRedirect(route('settings.website.index'));
        $response->assertSessionHas('success');

        $this->company->refresh();

        $this->assertEquals('corporate@emerald.local', $this->company->email);
        $this->assertEquals('021-55443322', $this->company->phone);
        $this->assertIsArray($this->company->website_settings);
        $this->assertEquals('Royal Emerald Luxury Portal', $this->company->website_settings['site_title']);
        $this->assertEquals('081299887766', $this->company->website_settings['whatsapp']);
        $this->assertTrue($this->company->website_settings['announcement_active']);
        $this->assertNotNull($this->company->logo_path);
        Storage::disk('public')->assertExists($this->company->logo_path);
    }

    public function test_super_admin_can_access_website_settings_page(): void
    {
        $superAdmin = User::create([
            'company_id' => $this->company->id,
            'name' => 'Super Administrator',
            'email' => 'superadmin@propflow.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SUPER_ADMIN,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('settings.website.index'));

        $response->assertOk();
    }

    public function test_sales_agent_cannot_access_website_settings(): void
    {
        $response = $this->actingAs($this->sales)->get(route('settings.website.index'));

        $response->assertForbidden();
    }

    public function test_finance_user_cannot_access_website_settings(): void
    {
        $finance = User::create([
            'company_id' => $this->company->id,
            'name' => 'Finance Staff',
            'email' => 'finance@emerald.local',
            'password' => bcrypt('password'),
            'role' => UserRole::FINANCE,
            'is_active' => true,
        ]);

        $response = $this->actingAs($finance)->get(route('settings.website.index'));

        $response->assertForbidden();
    }
}
