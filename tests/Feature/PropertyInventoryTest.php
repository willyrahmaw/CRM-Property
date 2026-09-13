<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Cluster;
use App\Models\Company;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyInventoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'PT Test Grand Property',
            'code' => 'TEST-PROP',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'company_id' => $this->company->id,
            'name' => 'Admin Properti',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'role' => UserRole::SUPER_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_projects_page_displays_project_with_image(): void
    {
        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Emerald City',
            'slug' => 'grand-emerald-city',
            'developer_name' => 'Emerald Developer',
            'city' => 'Tangerang',
            'description' => 'Hunian mewah eksklusif',
            'status' => ProjectStatus::ACTIVE,
            'image' => 'images/properties/project_grand_harmony.jpg',
        ]);

        $response = $this->actingAs($this->admin)->get(route('inventory.projects.index'));

        $response->assertOk();
        $response->assertSee('Grand Emerald City');
        $response->assertSee('images/properties/project_grand_harmony.jpg');
    }

    public function test_unit_creation_with_photo_upload(): void
    {
        Storage::fake('public');

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Emerald Residence',
            'slug' => 'emerald-residence',
            'city' => 'Bogor',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $cluster = Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Rose',
            'code' => 'RSE',
        ]);

        $fakePhoto = UploadedFile::fake()->image('unit_facade.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('inventory.units.store'), [
            'cluster_id' => $cluster->id,
            'unit_number' => 'B-09',
            'block' => 'B',
            'land_area' => 120,
            'building_area' => 85,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floors' => 2,
            'selling_price' => 1250000000,
            'image' => $fakePhoto,
        ]);

        $response->assertRedirect(route('inventory.units.index'));

        $unit = PropertyUnit::where('unit_number', 'B-09')->first();
        $this->assertNotNull($unit);
        $this->assertNotNull($unit->image);
        Storage::disk('public')->assertExists($unit->image);
    }

    public function test_unit_creation_with_complete_physical_and_building_specifications(): void
    {
        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Royal Sapphire Residence',
            'slug' => 'royal-sapphire',
            'city' => 'Jakarta Selatan',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $cluster = Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Platinum',
            'code' => 'PLT',
        ]);

        $postData = [
            'cluster_id' => $cluster->id,
            'unit_number' => 'PLT-01',
            'block' => 'A',
            'land_area' => 120,
            'dimension' => '8 x 15 m',
            'building_area' => 150,
            'bedrooms' => 4,
            'bathrooms' => 3,
            'floors' => 2,
            'carports' => 2,
            'direction' => 'Selatan',
            'electricity' => '3.500 VA',
            'water_source' => 'PDAM Kota + Toren Cadangan',
            'certificate_type' => 'SHM (Sertifikat Hak Milik)',
            'building_specs' => [
                'foundation' => 'Batu Kali & Mini Pile, Struktur Beton Bertulang SNI',
                'wall' => 'Bata Merah Plester Aci, Cat Weather Shield',
                'roof' => 'Rangka Baja Ringan, Genteng Beton Flat Monier',
                'floor' => 'Homogeneous Tile 60x60 cm, Kamar Parket Vinyl',
                'doors_windows' => 'Aluminium Powder Coating, Pintu Solid Wood',
                'sanitary' => 'Kloset Duduk Toto, Shower Set',
                'smart_features' => 'Smart Digital Door Lock, Canopy Carport Minimalis',
            ],
            'selling_price' => 2100000000,
            'promo' => 'Free BPHTB & Subsidi DP 10%',
            'notes' => 'Unit premium posisi hook menghadap taman utama.',
        ];

        $response = $this->actingAs($this->admin)->post(route('inventory.units.store'), $postData);

        $response->assertRedirect(route('inventory.units.index'));

        $unit = PropertyUnit::where('unit_number', 'PLT-01')->first();
        $this->assertNotNull($unit);
        $this->assertEquals('8 x 15 m', $unit->dimension);
        $this->assertEquals(2, $unit->carports);
        $this->assertEquals('3.500 VA', $unit->electricity);
        $this->assertEquals('PDAM Kota + Toren Cadangan', $unit->water_source);
        $this->assertEquals('SHM (Sertifikat Hak Milik)', $unit->certificate_type);
        $this->assertIsArray($unit->building_specs);
        $this->assertEquals('Batu Kali & Mini Pile, Struktur Beton Bertulang SNI', $unit->building_specs['foundation']);

        // Test that show page renders these specifications cleanly
        $showResponse = $this->actingAs($this->admin)->get(route('inventory.units.show', $unit));
        $showResponse->assertOk();
        $showResponse->assertSee('8 x 15 m');
        $showResponse->assertSee('3.500 VA');
        $showResponse->assertSee('Batu Kali &amp; Mini Pile', false);
        $showResponse->assertSee('Homogeneous Tile 60x60 cm');
        $showResponse->assertSee('Smart Digital Door Lock');
        $showResponse->assertSee('Free BPHTB &amp; Subsidi DP 10%', false);
    }

    public function test_can_edit_and_update_project(): void
    {
        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Proyek Awal Typo',
            'slug' => 'proyek-awal-typo',
            'developer_name' => 'Developer Awal',
            'city' => 'Bogor',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('inventory.projects.edit', $project));
        $editResponse->assertOk();
        $editResponse->assertSee('Proyek Awal Typo');

        $updateResponse = $this->actingAs($this->admin)->put(route('inventory.projects.update', $project), [
            'name' => 'Proyek Revisi Sempurna',
            'developer_name' => 'Developer Resmi',
            'city' => 'Bogor Selatan',
            'address' => 'Jl. Baru No. 10',
            'description' => 'Deskripsi kawasan hunian yang sudah diperbaiki',
            'status' => ProjectStatus::ACTIVE->value,
        ]);

        $updateResponse->assertRedirect(route('inventory.projects.show', $project));

        $project->refresh();
        $this->assertEquals('Proyek Revisi Sempurna', $project->name);
        $this->assertEquals('Developer Resmi', $project->developer_name);
        $this->assertEquals('Bogor Selatan', $project->city);
    }

    public function test_can_delete_project_without_active_transactions(): void
    {
        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Proyek Salah Input',
            'slug' => 'proyek-salah-input',
            'developer_name' => 'Developer Test',
            'city' => 'Depok',
            'status' => ProjectStatus::PLANNING,
        ]);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('inventory.projects.destroy', $project));
        $deleteResponse->assertRedirect(route('inventory.projects.index'));

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_can_create_cluster_with_multiple_photos(): void
    {
        Storage::fake('public');

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Emerald City',
            'slug' => 'grand-emerald-city-cluster-test',
            'city' => 'Tangerang',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $photo1 = UploadedFile::fake()->image('cluster_gate.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('cluster_park.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('inventory.clusters.store'), [
            'project_id' => $project->id,
            'name' => 'Cluster Magnolia Garden',
            'code' => 'MGN',
            'description' => 'Cluster bernuansa taman tropis asri.',
            'photos' => [$photo1, $photo2],
        ]);

        $response->assertRedirect();

        $cluster = Cluster::where('name', 'Cluster Magnolia Garden')->first();
        $this->assertNotNull($cluster);
        $this->assertCount(2, $cluster->photos);

        foreach ($cluster->photos as $path) {
            Storage::disk('public')->assertExists($path);
            $this->assertStringEndsWith('.webp', $path);
        }
    }

    public function test_can_update_cluster_photos_and_delete_specific_photo(): void
    {
        Storage::fake('public');

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Emerald City',
            'slug' => 'grand-emerald-city-update-test',
            'city' => 'Tangerang',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $photo1Path = UploadedFile::fake()->image('old1.jpg')->store('properties/clusters', 'public');
        $photo2Path = UploadedFile::fake()->image('old2.jpg')->store('properties/clusters', 'public');

        $cluster = Cluster::create([
            'project_id' => $project->id,
            'name' => 'Cluster Jasmine',
            'code' => 'JAS',
            'photos' => [$photo1Path, $photo2Path],
        ]);

        $newPhoto = UploadedFile::fake()->image('new3.jpg');

        $response = $this->actingAs($this->admin)->put(route('inventory.clusters.update', $cluster), [
            'project_id' => $project->id,
            'name' => 'Cluster Jasmine Executive',
            'code' => 'JAS-EX',
            'delete_photos' => [$photo1Path],
            'photos' => [$newPhoto],
        ]);

        $response->assertRedirect();

        $cluster->refresh();
        $this->assertEquals('Cluster Jasmine Executive', $cluster->name);
        $this->assertCount(2, $cluster->photos); // photo2 + newPhoto
        $this->assertNotContains($photo1Path, $cluster->photos);
        $this->assertContains($photo2Path, $cluster->photos);

        Storage::disk('public')->assertMissing($photo1Path);
        Storage::disk('public')->assertExists($photo2Path);
    }
}


