<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Enums\NegotiationApprovalStatus;
use App\Enums\PaymentScheme;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProjectStatus;
use App\Enums\PropertyUnitStatus;
use App\Enums\SiteVisitStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Commission;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Negotiation;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PropertyType;
use App\Models\PropertyUnit;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Developer Company
        $company = Company::updateOrCreate(
            ['code' => 'GH-LAND'],
            [
                'name' => 'PT Grand Harmony Land',
                'email' => 'contact@grandharmony.co.id',
                'phone' => '021-55889900',
                'address' => 'Grand Harmony Tower Lt. 18, Jl. Boulevard Barat No. 88, BSD City',
                'website_settings' => [
                    'site_title' => 'Grand Harmony Land — Luxury Living',
                    'tagline' => 'Hunian Mewah Eksklusif & Investasi Masa Depan',
                    'about_text' => 'PT Grand Harmony Land adalah pengembang properti terkemuka dengan reputasi keunggulan arsitektur dan komitmen kepuasan konsumen.',
                    'email' => 'contact@grandharmony.co.id',
                    'phone' => '021-55889900',
                    'whatsapp' => '081199887766',
                    'address' => 'Grand Harmony Tower Lt. 18, Jl. Boulevard Barat No. 88, BSD City',
                    'operational_hours' => 'Senin - Minggu: 08:30 - 18:00 WIB',
                    'announcement_active' => true,
                    'announcement_text' => 'Promo Spesial: Subsidi DP 10% & Bebas Biaya KPR s/d Akhir Periode!',
                    'instagram' => '@grandharmony.id',
                    'facebook' => 'https://facebook.com/grandharmonyland',
                    'youtube' => 'https://youtube.com/@grandharmonyland',
                    'tiktok' => '@grandharmony.property',
                    'meta_title' => 'Grand Harmony Land — Official Developer & Perumahan Mewah BSD',
                    'meta_description' => 'Temukan hunian kaveling & townhouse eksklusif Grand Harmony Land dengan cicilan KPR terjangkau & fasilitas bintang lima.',
                    'meta_keywords' => 'perumahan bsd, rumah mewah tangerang, kpr properti murah',
                    'google_analytics_id' => 'G-GRANDH01',
                    'facebook_pixel_id' => '987654321012345',
                ],
                'is_active' => true,
            ]
        );

        // 2. Create Users across Roles
        $defaultPassword = Hash::make('CrmProperty123!');

        $owner = User::create([
            'company_id' => $company->id,
            'name' => 'Bambang Wijaya',
            'email' => 'owner@propflow.local',
            'phone' => '081122334455',
            'password' => $defaultPassword,
            'role' => UserRole::COMPANY_OWNER,
            'is_active' => true,
        ]);

        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Hendrik Pratama',
            'email' => 'manager@propflow.local',
            'phone' => '081233445566',
            'password' => $defaultPassword,
            'role' => UserRole::SALES_MANAGER,
            'is_active' => true,
        ]);

        $finance = User::create([
            'company_id' => $company->id,
            'name' => 'Siti Rahmawati',
            'email' => 'finance@propflow.local',
            'phone' => '081344556677',
            'password' => $defaultPassword,
            'role' => UserRole::FINANCE,
            'is_active' => true,
        ]);

        $adminProp = User::create([
            'company_id' => $company->id,
            'name' => 'Dedi Irawan',
            'email' => 'admin@propflow.local',
            'phone' => '081455667788',
            'password' => $defaultPassword,
            'role' => UserRole::ADMIN_PROPERTY,
            'is_active' => true,
        ]);

        $sales1 = User::create([
            'company_id' => $company->id,
            'name' => 'Andi Setiawan',
            'email' => 'andi@propflow.local',
            'phone' => '081566778899',
            'password' => $defaultPassword,
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $sales2 = User::create([
            'company_id' => $company->id,
            'name' => 'Rina Melati',
            'email' => 'rina@propflow.local',
            'phone' => '081677889900',
            'password' => $defaultPassword,
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $sales3 = User::create([
            'company_id' => $company->id,
            'name' => 'Dimas Saputra',
            'email' => 'dimas@propflow.local',
            'phone' => '081788990011',
            'password' => $defaultPassword,
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $sales4 = User::create([
            'company_id' => $company->id,
            'name' => 'Maya Anggraini',
            'email' => 'maya@propflow.local',
            'phone' => '081899001122',
            'password' => $defaultPassword,
            'role' => UserRole::SALES_AGENT,
            'is_active' => true,
        ]);

        $sales5 = User::create([
            'company_id' => $company->id,
            'name' => 'Fajar Nugroho',
            'email' => 'fajar@propflow.local',
            'phone' => '081900112233',
            'password' => $defaultPassword,
            'role' => UserRole::TEAM_LEADER,
            'is_active' => true,
        ]);

        $allSales = collect([$sales1, $sales2, $sales3, $sales4, $sales5]);

        // 3. Create 3 Property Projects
        $proj1 = Project::create([
            'company_id' => $company->id,
            'name' => 'Grand Residence BSD',
            'slug' => 'grand-residence-bsd',
            'developer_name' => 'PT Grand Harmony Land',
            'city' => 'Tangerang Selatan',
            'address' => 'Kawasan Serpong Garden, BSD City',
            'latitude' => -6.3012345,
            'longitude' => 106.6890123,
            'description' => 'Kawasan hunian premium berkonsep Eco-Modern Sanctuary dengan akses langsung tol Serpong-Balaraja.',
            'facilities' => ['Clubhouse', 'Swimming Pool', 'Underground Utilities', 'Smart Home System', '24h Security'],
            'image' => 'images/properties/project_grand_harmony.jpg',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $proj2 = Project::create([
            'company_id' => $company->id,
            'name' => 'Royal Emerald Hills',
            'slug' => 'royal-emerald-hills',
            'developer_name' => 'PT Grand Harmony Land',
            'city' => 'Bogor',
            'address' => 'Kawasan Bukit Sentul Selatan',
            'latitude' => -6.5812345,
            'longitude' => 106.8890123,
            'description' => 'Hunian resort pegunungan berhawa sejuk dengan pemandangan Gunung Salak dan fasilitas lapangan golf.',
            'facilities' => ['Jogging Track', 'Infinity Pool', 'Children Playground', 'CCTV 24 Jam'],
            'image' => 'images/properties/project_grand_harmony.jpg',
            'status' => ProjectStatus::ACTIVE,
        ]);

        $proj3 = Project::create([
            'company_id' => $company->id,
            'name' => 'The Crown Palace',
            'slug' => 'the-crown-palace',
            'developer_name' => 'PT Grand Harmony Land',
            'city' => 'Jakarta Timur',
            'address' => 'Jl. Alternatif Cibubur Km. 4',
            'latitude' => -6.3712345,
            'longitude' => 106.9190123,
            'description' => 'Townhouse mewah 3 lantai bergaya modern kontemporer di jantung pusat bisnis Cibubur.',
            'facilities' => ['Private Lift Option', 'Rooftop Lounge', 'Solar Panel Ready'],
            'image' => 'images/properties/project_grand_harmony.jpg',
            'status' => ProjectStatus::PRE_LAUNCH,
        ]);

        // 4. Create 5 Clusters
        $cl1 = Cluster::create([
            'project_id' => $proj1->id,
            'name' => 'Cluster Magnolia',
            'code' => 'MAG',
            'description' => 'Cluster tahap 1 berkonsep tropical modern dengan private courtyard.',
        ]);

        $cl2 = Cluster::create([
            'project_id' => $proj1->id,
            'name' => 'Cluster Jasmine',
            'code' => 'JAS',
            'description' => 'Cluster eksklusif menghadap danau buatan dan clubhouse utama.',
        ]);

        $cl3 = Cluster::create([
            'project_id' => $proj2->id,
            'name' => 'Cluster Pinewood',
            'code' => 'PIN',
            'description' => 'Rumah villa dengan pemandangan perbukitan pinus.',
        ]);

        $cl4 = Cluster::create([
            'project_id' => $proj2->id,
            'name' => 'Cluster Oakwood',
            'code' => 'OAK',
            'description' => 'Cluster kontur bertingkat dengan desain split level.',
        ]);

        $cl5 = Cluster::create([
            'project_id' => $proj3->id,
            'name' => 'Cluster Imperial Crown',
            'code' => 'IMP',
            'description' => 'Mansion megah 3 lantai dengan double height ceiling.',
        ]);

        $allClusters = collect([$cl1, $cl2, $cl3, $cl4, $cl5]);

        // 5. Create Property Types
        $typeA = PropertyType::create([
            'project_id' => $proj1->id,
            'name' => 'Type 45/90 — Aster',
            'code' => 'T-45',
            'building_area' => 45,
            'land_area' => 90,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'floors' => 1,
        ]);

        $typeB = PropertyType::create([
            'project_id' => $proj1->id,
            'name' => 'Type 68/105 — Bougenville (2 Lantai)',
            'code' => 'T-68',
            'building_area' => 68,
            'land_area' => 105,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floors' => 2,
        ]);

        $typeC = PropertyType::create([
            'project_id' => $proj2->id,
            'name' => 'Type 90/135 — Camellia Villa',
            'code' => 'T-90',
            'building_area' => 90,
            'land_area' => 135,
            'bedrooms' => 3,
            'bathrooms' => 3,
            'floors' => 2,
        ]);

        // 6. Create 50 Property Units with Realistic Statuses
        $unitStatuses = [
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::AVAILABLE,
            PropertyUnitStatus::RESERVED,
            PropertyUnitStatus::BOOKED,
            PropertyUnitStatus::SOLD,
        ];

        $units = collect();
        $unitCounter = 1;

        foreach ($allClusters as $cluster) {
            for ($i = 1; $i <= 10; $i++) {
                $status = $unitStatuses[array_rand($unitStatuses)];
                $unitNumber = sprintf('%s-%02d', $cluster->code, $i);

                $unit = PropertyUnit::create([
                    'cluster_id' => $cluster->id,
                    'property_type_id' => $typeA->id,
                    'unit_number' => $unitNumber,
                    'block' => chr(65 + ($unitCounter % 5)), // Blok A - E
                    'land_area' => 90 + ($i * 5),
                    'dimension' => sprintf('%d x %d m', 6 + ($i % 3), 15 + ($i % 2)),
                    'building_area' => 45 + ($i * 3),
                    'bedrooms' => 2 + ($i % 2),
                    'bathrooms' => 1 + ($i % 2),
                    'floors' => 1 + ($i % 2),
                    'carports' => 1 + ($i % 2),
                    'direction' => ($i % 2 === 0) ? 'Utara' : 'Selatan',
                    'electricity' => ($i % 2 === 0) ? '2.200 VA' : '3.500 VA',
                    'water_source' => 'PDAM Kota + Toren Cadangan',
                    'certificate_type' => 'SHM (Sertifikat Hak Milik)',
                    'building_specs' => [
                        'foundation' => 'Batu Kali & Mini Pile, Struktur Beton Bertulang SNI',
                        'wall' => 'Bata Merah Diplester & Aci Halus, Cat Weather Shield Premium',
                        'roof' => 'Rangka Baja Ringan Zincalume, Genteng Flat Beton Monier',
                        'floor' => 'Homogeneous Tile 60x60 cm Glazed, Lantai Kamar Parket Vinyl',
                        'doors_windows' => 'Kusen Aluminium Powder Coating, Pintu Solid Engineering Wood',
                        'sanitary' => 'Kloset Duduk Toto, Shower Spray Set & Wastafel Toto',
                        'smart_features' => 'Smart Digital Door Lock, Canopy Carport Minimalis, Free Toren Air 500L',
                    ],
                    'base_price' => 600000000 + ($unitCounter * 15000000),
                    'selling_price' => 650000000 + ($unitCounter * 15000000),
                    'status' => $status,
                    'promo' => 'Free BPHTB & AJB, Subsidi DP 5%',
                    'notes' => 'Unit siap bangun, estimasi serah terima kunci 12 bulan.',
                ]);

                $units->push($unit);
                $unitCounter++;
            }
        }

        // 7. Create 10 Realistic Customers
        $customerData = [
            ['name' => 'Dr. Irwan Setiadi', 'gender' => 'male', 'phone' => '081290901111', 'email' => 'irwan.setiadi@gmail.com', 'nik' => '3276011203850001', 'occupation' => 'Dokter Spesialis'],
            ['name' => 'Hj. Nurul Aini', 'gender' => 'female', 'phone' => '081380802222', 'email' => 'nurul.aini@yahoo.com', 'nik' => '3276012408900002', 'occupation' => 'Pengusaha Tekstil'],
            ['name' => 'Kevin Pratama, S.T.', 'gender' => 'male', 'phone' => '081170703333', 'email' => 'kevin.pratama@tech.co.id', 'nik' => '3174021501930003', 'occupation' => 'Software Architect'],
            ['name' => 'Agus Hartono', 'gender' => 'male', 'phone' => '081560604444', 'email' => 'agus.hartono@gmail.com', 'nik' => '3201010505820004', 'occupation' => 'Direktur Operasional'],
            ['name' => 'Dewi Lestari', 'gender' => 'female', 'phone' => '081650505555', 'email' => 'dewi.lestari@corporate.id', 'nik' => '3275031907880005', 'occupation' => 'Finance Manager'],
            ['name' => 'Bambang Kusumo', 'gender' => 'male', 'phone' => '081740406666', 'email' => 'bambang.k@outlook.com', 'nik' => '3171010109790006', 'occupation' => 'Notaris & PPAT'],
            ['name' => 'Ratna Sari', 'gender' => 'female', 'phone' => '081830307777', 'email' => 'ratna.sari@gmail.com', 'nik' => '3276011111870007', 'occupation' => 'Konsultan Bisnis'],
            ['name' => 'Taufik Hidayat', 'gender' => 'male', 'phone' => '081920208888', 'email' => 'taufik.hidayat@gmail.com', 'nik' => '3201021406840008', 'occupation' => 'Wiraswasta'],
            ['name' => 'Citra Kirana', 'gender' => 'female', 'phone' => '081210109999', 'email' => 'citra.kirana@gmail.com', 'nik' => '3175052203920009', 'occupation' => 'Marketing Director'],
            ['name' => 'Eko Prasetyo', 'gender' => 'male', 'phone' => '081300001010', 'email' => 'eko.prasetyo@gmail.com', 'nik' => '3275010909860010', 'occupation' => 'Senior Engineer'],
        ];

        $customers = collect();
        foreach ($customerData as $c) {
            $customer = Customer::create([
                'company_id' => $company->id,
                'nik' => $c['nik'],
                'name' => $c['name'],
                'gender' => $c['gender'],
                'phone' => $c['phone'],
                'email' => $c['email'],
                'address' => 'Jl. Boulevard Raya No. ' . rand(1, 100) . ', Jakarta Selatan',
                'occupation' => $c['occupation'],
            ]);
            $customers->push($customer);
        }

        // 8. Create 30 Realistic Leads
        $leadSources = [
            LeadSource::FACEBOOK_ADS,
            LeadSource::GOOGLE_ADS,
            LeadSource::INSTAGRAM,
            LeadSource::TIKTOK,
            LeadSource::WHATSAPP,
            LeadSource::WALK_IN,
            LeadSource::WEBSITE,
        ];

        $leadNames = [
            'Ahmad Fauzi', 'Siti Maryam', 'Reza Rahadian', 'Dian Sastro', 'Nicholas Saputra',
            'Chelsea Islan', 'Chicco Jerikho', 'Pevita Pearce', 'Gading Marten', 'Raffi Ahmad',
            'Nagita Slavina', 'Atta Halilintar', 'Aurel Hermansyah', 'Deddy Corbuzier', 'Najwa Shihab',
            'Raditya Dika', 'Vidi Aldiano', 'Sheila Dara', 'Prilly Latuconsina', 'Iqbaal Ramadhan',
            'Angga Yunanda', 'Shenina Cinnamon', 'Jefri Nichol', 'Laura Basuki', 'Tara Basro',
            'Rio Dewanto', 'Atiqah Hasiholan', 'Vino G. Bastian', 'Marsha Timothy', 'Adipati Dolken'
        ];

        $femaleNames = [
            'Siti Maryam', 'Dian Sastro', 'Chelsea Islan', 'Pevita Pearce',
            'Nagita Slavina', 'Aurel Hermansyah', 'Najwa Shihab', 'Sheila Dara',
            'Prilly Latuconsina', 'Shenina Cinnamon', 'Laura Basuki', 'Tara Basro',
            'Atiqah Hasiholan', 'Marsha Timothy'
        ];

        $leads = collect();
        foreach ($leadNames as $index => $leadName) {
            $assignedSales = $allSales[$index % $allSales->count()];
            $source = $leadSources[$index % count($leadSources)];
            $gender = in_array($leadName, $femaleNames) ? 'female' : 'male';

            // Scores & Statuses
            $status = match (true) {
                $index < 5 => LeadStatus::NEW,
                $index < 10 => LeadStatus::CONTACTED,
                $index < 16 => LeadStatus::QUALIFIED,
                $index < 21 => LeadStatus::SITE_VISIT,
                $index < 25 => LeadStatus::NEGOTIATION,
                $index < 28 => LeadStatus::WON,
                default => LeadStatus::LOST,
            };

            $score = match ($status) {
                LeadStatus::WON, LeadStatus::NEGOTIATION => rand(75, 95),
                LeadStatus::SITE_VISIT, LeadStatus::QUALIFIED => rand(45, 68),
                default => rand(15, 38),
            };

            $temperature = match (true) {
                $score >= 70 => LeadTemperature::HOT,
                $score >= 40 => LeadTemperature::WARM,
                default => LeadTemperature::COLD,
            };

            $lead = Lead::create([
                'company_id' => $company->id,
                'assigned_sales_id' => $assignedSales->id,
                'code' => sprintf('LD-202603-%04d', $index + 1),
                'name' => $leadName,
                'gender' => $gender,
                'phone' => '0812' . rand(10000000, 99999999),
                'email' => strtolower(str_replace(' ', '.', $leadName)) . '@gmail.com',
                'source' => $source,
                'campaign' => 'Meta Ads Q1 - Promo Bunga KPR 2.5%',
                'budget_min' => 600000000,
                'budget_max' => 900000000,
                'interested_project_id' => $proj1->id,
                'property_type_interest' => 'Type 45 / 2 Kamar',
                'status' => $status,
                'temperature' => $temperature,
                'score' => $score,
                'purchase_target_days' => ($score > 60) ? 14 : 30,
                'lost_reason' => ($status === LeadStatus::LOST) ? 'Harga melebihi estimasi anggaran keluarga.' : null,
                'notes' => 'Mencari rumah pertama dekat stasiun KRL / akses tol.',
            ]);

            $leads->push($lead);

            // Create activities for each lead
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $assignedSales->id,
                'activity_type' => 'whatsapp',
                'activity_date' => now()->subDays(rand(1, 10)),
                'result' => 'Sudah mengirimkan e-brochure dan simulasi tabel angsuran bank.',
                'notes' => 'Customer merespons positif dan meminta waktu survei lokasi weekend.',
                'next_follow_up_date' => now()->addDays(2),
            ]);
        }

        // 9. Create 6 Bookings with Payments and Commissions
        $bookedUnits = $units->whereIn('status', [PropertyUnitStatus::BOOKED, PropertyUnitStatus::SOLD])->values();

        for ($b = 0; $b < min(6, $bookedUnits->count()); $b++) {
            $targetUnit = $bookedUnits[$b];
            $customer = $customers[$b];
            $sales = $allSales[$b % $allSales->count()];

            $booking = Booking::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'property_unit_id' => $targetUnit->id,
                'sales_id' => $sales->id,
                'booking_number' => sprintf('BKG-202603-%04d', $b + 1),
                'booking_date' => now()->subDays(10 - $b)->toDateString(),
                'booking_fee' => 10000000,
                'unit_price' => $targetUnit->selling_price,
                'discount_amount' => 10000000,
                'final_price' => $targetUnit->selling_price - 10000000,
                'payment_scheme' => ($b % 2 === 0) ? PaymentScheme::KPR : PaymentScheme::CASH_BERTAHAP,
                'status' => ($b < 4) ? BookingStatus::APPROVED : BookingStatus::PENDING,
                'approved_by_id' => ($b < 4) ? $owner->id : null,
                'notes' => 'Surat pesanan resmi telah ditandatangani konsumen.',
            ]);

            // Create Verified Payment for booking fee
            Payment::create([
                'booking_id' => $booking->id,
                'verified_by_id' => $finance->id,
                'payment_number' => sprintf('PAY-202603-%04d', $b + 1),
                'payment_type' => PaymentType::BOOKING_FEE,
                'amount' => 10000000,
                'payment_date' => now()->subDays(9 - $b)->toDateString(),
                'payment_method' => 'Transfer Bank BCA',
                'reference_number' => 'TRX-' . rand(100000, 999999),
                'status' => PaymentStatus::VERIFIED,
                'verified_at' => now()->subDays(8 - $b),
                'notes' => 'Dana masuk rekening BCA PT Grand Harmony Land.',
            ]);

            // Generate Commissions
            $totalComm = ($booking->final_price * 2.5) / 100;
            Commission::create([
                'booking_id' => $booking->id,
                'user_id' => $sales->id,
                'beneficiary_type' => 'sales',
                'selling_price' => $booking->final_price,
                'percentage' => 1.5,
                'amount' => ($totalComm * 0.60),
                'status' => ($b < 2) ? CommissionStatus::PAID : CommissionStatus::APPROVED,
                'approved_by_id' => $finance->id,
                'paid_at' => ($b < 2) ? now()->subDays(2)->toDateString() : null,
            ]);

            Commission::create([
                'booking_id' => $booking->id,
                'user_id' => $sales5->id, // Team Leader
                'beneficiary_type' => 'team_leader',
                'selling_price' => $booking->final_price,
                'percentage' => 0.5,
                'amount' => ($totalComm * 0.20),
                'status' => CommissionStatus::APPROVED,
                'approved_by_id' => $finance->id,
            ]);
        }

        // 10. Create 5 Site Visits
        for ($v = 0; $v < 5; $v++) {
            SiteVisit::create([
                'lead_id' => $leads[$v + 15]->id,
                'user_id' => $allSales[$v % $allSales->count()]->id,
                'project_id' => $proj1->id,
                'property_unit_id' => $units[$v]->id,
                'visit_date' => now()->addDays($v + 1)->setTime(10 + $v, 0),
                'status' => SiteVisitStatus::SCHEDULED,
                'notes' => 'Customer ingin melihat langsung show unit tipe 45.',
            ]);
        }

        // 11. Create Realistic Negotiations & Discounts
        Negotiation::create([
            'lead_id' => $leads[2]->id,
            'property_unit_id' => $units[7]->id,
            'sales_id' => $sales1->id,
            'initial_price' => $units[7]->selling_price,
            'customer_offer_price' => $units[7]->selling_price - 25000000,
            'final_price' => $units[7]->selling_price - 25000000,
            'discount_amount' => 25000000,
            'discount_percentage' => round((25000000 / $units[7]->selling_price) * 100, 2),
            'promo_description' => 'Promo Spesial Gathering Akhir Pekan',
            'approval_status' => NegotiationApprovalStatus::PENDING,
            'notes' => 'Konsumen siap bayar booking fee hari ini jika diskon 25jt disetujui.',
        ]);

        Negotiation::create([
            'lead_id' => $leads[5]->id,
            'property_unit_id' => $units[8]->id,
            'sales_id' => $sales2->id,
            'initial_price' => $units[8]->selling_price,
            'customer_offer_price' => $units[8]->selling_price - 15000000,
            'final_price' => $units[8]->selling_price - 15000000,
            'discount_amount' => 15000000,
            'discount_percentage' => round((15000000 / $units[8]->selling_price) * 100, 2),
            'promo_description' => 'Subsidi DP & Biaya Notaris',
            'approval_status' => NegotiationApprovalStatus::APPROVED,
            'approved_by_id' => $manager->id,
            'notes' => 'Disetujui manajer untuk percepatan penutupan penjualan kaveling boulevard.',
        ]);

        Negotiation::create([
            'lead_id' => $leads[8]->id,
            'property_unit_id' => $units[9]->id,
            'sales_id' => $sales3->id,
            'initial_price' => $units[9]->selling_price,
            'customer_offer_price' => $units[9]->selling_price - 80000000,
            'final_price' => $units[9]->selling_price - 80000000,
            'discount_amount' => 80000000,
            'discount_percentage' => round((80000000 / $units[9]->selling_price) * 100, 2),
            'promo_description' => 'Permintaan Diskon Cash Keras',
            'approval_status' => NegotiationApprovalStatus::REJECTED,
            'approved_by_id' => $manager->id,
            'notes' => 'Ditolak: Diskon 80jt melebihi margin batas toleransi promo developer (maks 30jt).',
        ]);
    }
}
