<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\PropertyUnitStatus;
use App\Models\Cluster;
use App\Models\Company;
use App\Models\Project;
use App\Models\PropertyType;
use App\Models\PropertyUnit;
use Illuminate\Database\Seeder;

class ProjectAndUnitSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();

        // 1. Projects
        $proj1 = Project::updateOrCreate(
            ['slug' => 'grand-residence-bsd'],
            [
                'company_id' => $company->id,
                'name' => 'Grand Residence BSD',
                'developer_name' => 'PT Grand Harmony Land',
                'city' => 'Tangerang Selatan',
                'address' => 'Kawasan Serpong Garden, BSD City',
                'latitude' => -6.3012345,
                'longitude' => 106.6890123,
                'description' => 'Kawasan hunian premium berkonsep Eco-Modern Sanctuary dengan akses langsung tol Serpong-Balaraja.',
                'facilities' => ['Clubhouse', 'Swimming Pool', 'Underground Utilities', 'Smart Home System', '24h Security'],
                'image' => 'images/properties/project_grand_harmony.webp',
                'status' => ProjectStatus::ACTIVE,
            ]
        );

        $proj2 = Project::updateOrCreate(
            ['slug' => 'royal-emerald-hills'],
            [
                'company_id' => $company->id,
                'name' => 'Royal Emerald Hills',
                'developer_name' => 'PT Grand Harmony Land',
                'city' => 'Bogor',
                'address' => 'Kawasan Bukit Sentul Selatan',
                'latitude' => -6.5812345,
                'longitude' => 106.8890123,
                'description' => 'Hunian resort pegunungan berhawa sejuk dengan pemandangan Gunung Salak dan fasilitas lapangan golf.',
                'facilities' => ['Jogging Track', 'Infinity Pool', 'Children Playground', 'CCTV 24 Jam'],
                'image' => 'images/properties/project_grand_harmony.webp',
                'status' => ProjectStatus::ACTIVE,
            ]
        );

        $proj3 = Project::updateOrCreate(
            ['slug' => 'the-crown-palace'],
            [
                'company_id' => $company->id,
                'name' => 'The Crown Palace',
                'developer_name' => 'PT Grand Harmony Land',
                'city' => 'Jakarta Timur',
                'address' => 'Jl. Alternatif Cibubur Km. 4',
                'latitude' => -6.3712345,
                'longitude' => 106.9190123,
                'description' => 'Townhouse mewah 3 lantai bergaya modern kontemporer di jantung pusat bisnis Cibubur.',
                'facilities' => ['Private Lift Option', 'Rooftop Lounge', 'Solar Panel Ready'],
                'image' => 'images/properties/project_grand_harmony.webp',
                'status' => ProjectStatus::PRE_LAUNCH,
            ]
        );

        // 2. Clusters
        $cl1 = Cluster::updateOrCreate(
            ['project_id' => $proj1->id, 'code' => 'MAG'],
            [
                'name' => 'Cluster Magnolia',
                'description' => 'Cluster tahap 1 berkonsep tropical modern dengan private courtyard.',
            ]
        );

        $cl2 = Cluster::updateOrCreate(
            ['project_id' => $proj1->id, 'code' => 'JAS'],
            [
                'name' => 'Cluster Jasmine',
                'description' => 'Cluster eksklusif menghadap danau buatan dan clubhouse utama.',
            ]
        );

        $cl3 = Cluster::updateOrCreate(
            ['project_id' => $proj2->id, 'code' => 'PIN'],
            [
                'name' => 'Cluster Pinewood',
                'description' => 'Rumah villa dengan pemandangan perbukitan pinus.',
            ]
        );

        $cl4 = Cluster::updateOrCreate(
            ['project_id' => $proj2->id, 'code' => 'OAK'],
            [
                'name' => 'Cluster Oakwood',
                'description' => 'Cluster kontur bertingkat dengan desain split level.',
            ]
        );

        $cl5 = Cluster::updateOrCreate(
            ['project_id' => $proj3->id, 'code' => 'IMP'],
            [
                'name' => 'Cluster Imperial Crown',
                'description' => 'Mansion megah 3 lantai dengan double height ceiling.',
            ]
        );

        $allClusters = collect([$cl1, $cl2, $cl3, $cl4, $cl5]);

        // 3. Property Types
        $typeA = PropertyType::updateOrCreate(
            ['project_id' => $proj1->id, 'code' => 'T-45'],
            [
                'name' => 'Type 45/90 — Aster',
                'building_area' => 45,
                'land_area' => 90,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'floors' => 1,
            ]
        );

        $typeB = PropertyType::updateOrCreate(
            ['project_id' => $proj1->id, 'code' => 'T-68'],
            [
                'name' => 'Type 68/105 — Bougenville (2 Lantai)',
                'building_area' => 68,
                'land_area' => 105,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'floors' => 2,
            ]
        );

        $typeC = PropertyType::updateOrCreate(
            ['project_id' => $proj2->id, 'code' => 'T-90'],
            [
                'name' => 'Type 90/135 — Camellia Villa',
                'building_area' => 90,
                'land_area' => 135,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'floors' => 2,
            ]
        );

        // 4. Property Units with Specifications
        $unitStatuses = [
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::RESERVED,
            PropertyUnitStatus::BOOKED,
            PropertyUnitStatus::SOLD,
        ];

        $unitCounter = 1;

        foreach ($allClusters as $cluster) {
            for ($i = 1; $i <= 10; $i++) {
                $block = chr(65 + ($unitCounter % 5)); // Blok A, B, C, D, E
                $unitNumber = sprintf('%s-%02d', $block, $i);
                $status = $unitStatuses[$unitCounter % count($unitStatuses)];

                $isLarge = ($unitCounter % 3 === 0);
                $buildingArea = $isLarge ? 120 : ($unitCounter % 2 === 0 ? 68 : 45);
                $landArea = $isLarge ? 160 : ($unitCounter % 2 === 0 ? 105 : 90);
                $price = $isLarge ? 1850000000 : ($unitCounter % 2 === 0 ? 1250000000 : 750000000);

                PropertyUnit::updateOrCreate(
                    [
                        'cluster_id' => $cluster->id,
                        'unit_number' => $unitNumber,
                    ],
                    [
                        'property_type_id' => $isLarge ? $typeC->id : ($unitCounter % 2 === 0 ? $typeB->id : $typeA->id),
                        'block' => $block,
                        'building_area' => $buildingArea,
                        'land_area' => $landArea,
                        'bedrooms' => $isLarge ? 4 : ($unitCounter % 2 === 0 ? 3 : 2),
                        'bathrooms' => $isLarge ? 3 : ($unitCounter % 2 === 0 ? 2 : 1),
                        'floors' => $isLarge ? 2 : ($unitCounter % 2 === 0 ? 2 : 1),
                        'base_price' => $price * 0.85,
                        'selling_price' => $price,
                        'status' => $status,
                        'dimension' => $isLarge ? '10 x 16 m' : ($unitCounter % 2 === 0 ? '7 x 15 m' : '6 x 15 m'),
                        'carports' => $isLarge ? 2 : 1,
                        'electricity' => $isLarge ? '3.500 VA' : '2.200 VA (Standar)',
                        'water_source' => 'PDAM + Toren Air Cadangan',
                        'certificate_type' => 'SHM (Sertifikat Hak Milik)',
                        'direction' => ($i % 2 === 0) ? 'Utara' : 'Selatan',
                        'building_specs' => [
                            'foundation' => 'Batu Kali & Mini Pile, Struktur Beton Bertulang SNI',
                            'wall' => 'Bata Merah Plester Aci, Cat Weather Shield Eksterior',
                            'roof' => 'Rangka Baja Ringan Zincalume, Genteng Flat Beton Monier',
                            'floor' => 'Homogeneous Tile 60x60 cm Glazed, Kamar Parket Vinyl',
                            'door_window' => 'Aluminium Powder Coating, Pintu Solid Engineering Wood',
                            'sanitary' => 'Kloset Duduk Toto, Shower Spray Set & Wastafel Toto',
                            'features' => 'Smart Digital Door Lock, Canopy Carport Minimalis, Meja Dapur Granit + Sink',
                            'notes' => 'Posisi kaveling dekat taman utama kawasan.',
                        ],
                    ]
                );

                $unitCounter++;
            }
        }
    }
}
