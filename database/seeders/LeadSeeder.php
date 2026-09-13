<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();
        $project = Project::firstOrFail();
        $salesAgents = User::where('company_id', $company->id)
            ->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->get();

        $leadSources = [
            LeadSource::FACEBOOK_ADS,
            LeadSource::GOOGLE_ADS,
            LeadSource::INSTAGRAM,
            LeadSource::TIKTOK,
            LeadSource::WHATSAPP,
            LeadSource::WALK_IN,
            LeadSource::WEBSITE,
        ];

        $leadProfiles = [
            ['name' => 'Ahmad Fauzi', 'gender' => Gender::MALE, 'phone' => '081211112222', 'email' => 'ahmad.fauzi@gmail.com'],
            ['name' => 'Siti Maryam', 'gender' => Gender::FEMALE, 'phone' => '081222223333', 'email' => 'siti.maryam@yahoo.com'],
            ['name' => 'Reza Rahadian', 'gender' => Gender::MALE, 'phone' => '081233334444', 'email' => 'reza.rahadian@gmail.com'],
            ['name' => 'Dian Sastrowardoyo', 'gender' => Gender::FEMALE, 'phone' => '081244445555', 'email' => 'dian.sastro@corporate.id'],
            ['name' => 'Nicholas Saputra', 'gender' => Gender::MALE, 'phone' => '081255556666', 'email' => 'nicholas.s@gmail.com'],
            ['name' => 'Chelsea Islan', 'gender' => Gender::FEMALE, 'phone' => '081266667777', 'email' => 'chelsea.islan@yahoo.com'],
            ['name' => 'Chicco Jerikho', 'gender' => Gender::MALE, 'phone' => '081277778888', 'email' => 'chicco.j@tech.id'],
            ['name' => 'Pevita Pearce', 'gender' => Gender::FEMALE, 'phone' => '081288889999', 'email' => 'pevita.pearce@gmail.com'],
            ['name' => 'Gading Marten', 'gender' => Gender::MALE, 'phone' => '081299990000', 'email' => 'gading.marten@gmail.com'],
            ['name' => 'Raffi Ahmad', 'gender' => Gender::MALE, 'phone' => '081311112222', 'email' => 'raffi.ahmad@rans.co.id'],
            ['name' => 'Nagita Slavina', 'gender' => Gender::FEMALE, 'phone' => '081322223333', 'email' => 'nagita.slavina@rans.co.id'],
            ['name' => 'Atta Halilintar', 'gender' => Gender::MALE, 'phone' => '081333334444', 'email' => 'atta.halilintar@gmail.com'],
            ['name' => 'Aurel Hermansyah', 'gender' => Gender::FEMALE, 'phone' => '081344445555', 'email' => 'aurel.h@yahoo.com'],
            ['name' => 'Deddy Corbuzier', 'gender' => Gender::MALE, 'phone' => '081355556666', 'email' => 'deddy.c@close.the.door'],
            ['name' => 'Najwa Shihab', 'gender' => Gender::FEMALE, 'phone' => '081366667777', 'email' => 'najwa.shihab@narasi.tv'],
            ['name' => 'Raditya Dika', 'gender' => Gender::MALE, 'phone' => '081377778888', 'email' => 'raditya.dika@gmail.com'],
            ['name' => 'Vidi Aldiano', 'gender' => Gender::MALE, 'phone' => '081388889999', 'email' => 'vidi.aldiano@gmail.com'],
            ['name' => 'Sheila Dara', 'gender' => Gender::FEMALE, 'phone' => '081399990000', 'email' => 'sheila.dara@corporate.id'],
            ['name' => 'Prilly Latuconsina', 'gender' => Gender::FEMALE, 'phone' => '081411112222', 'email' => 'prilly.latuconsina@sinemart.id'],
            ['name' => 'Iqbaal Ramadhan', 'gender' => Gender::MALE, 'phone' => '081422223333', 'email' => 'iqbaal.ramadhan@gmail.com'],
            ['name' => 'Angga Yunanda', 'gender' => Gender::MALE, 'phone' => '081433334444', 'email' => 'angga.yunanda@gmail.com'],
            ['name' => 'Shenina Cinnamon', 'gender' => Gender::FEMALE, 'phone' => '081444445555', 'email' => 'shenina.cinnamon@gmail.com'],
            ['name' => 'Jefri Nichol', 'gender' => Gender::MALE, 'phone' => '081455556666', 'email' => 'jefri.nichol@gmail.com'],
            ['name' => 'Laura Basuki', 'gender' => Gender::FEMALE, 'phone' => '081466667777', 'email' => 'laura.basuki@yahoo.com'],
            ['name' => 'Tara Basro', 'gender' => Gender::FEMALE, 'phone' => '081477778888', 'email' => 'tara.basro@art.id'],
            ['name' => 'Rio Dewanto', 'gender' => Gender::MALE, 'phone' => '081488889999', 'email' => 'rio.dewanto@filosofikopi.id'],
            ['name' => 'Atiqah Hasiholan', 'gender' => Gender::FEMALE, 'phone' => '081499990000', 'email' => 'atiqah.hasiholan@gmail.com'],
            ['name' => 'Vino G. Bastian', 'gender' => Gender::MALE, 'phone' => '081511112222', 'email' => 'vino.bastian@gmail.com'],
            ['name' => 'Marsha Timothy', 'gender' => Gender::FEMALE, 'phone' => '081522223333', 'email' => 'marsha.timothy@gmail.com'],
            ['name' => 'Adipati Dolken', 'gender' => Gender::MALE, 'phone' => '081533334444', 'email' => 'adipati.dolken@gmail.com'],
        ];

        foreach ($leadProfiles as $index => $profile) {
            $assignedSales = $salesAgents[$index % $salesAgents->count()];
            $source = $leadSources[$index % count($leadSources)];

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

            $code = sprintf('LD-202603-%04d', $index + 1);

            $lead = Lead::updateOrCreate(
                [
                    'code' => $code,
                ],
                [
                    'company_id' => $company->id,
                    'assigned_sales_id' => $assignedSales->id,
                    'name' => $profile['name'],
                    'gender' => $profile['gender'],
                    'phone' => $profile['phone'],
                    'email' => $profile['email'],
                    'source' => $source,
                    'campaign' => 'Meta Ads Q1 - Promo Bunga KPR 2.5%',
                    'budget_min' => 700000000,
                    'budget_max' => 1250000000,
                    'interested_project_id' => $project->id,
                    'property_type_interest' => 'Type 45 / 2 Kamar',
                    'status' => $status,
                    'temperature' => $temperature,
                    'score' => $score,
                    'purchase_target_days' => ($score > 60) ? 14 : 30,
                    'lost_reason' => ($status === LeadStatus::LOST) ? 'Harga melebihi estimasi anggaran keluarga.' : null,
                    'notes' => 'Mencari rumah pertama dekat stasiun KRL dan akses tol BSD.',
                ]
            );

            // Create activity log
            LeadActivity::firstOrCreate(
                [
                    'lead_id' => $lead->id,
                    'activity_type' => 'whatsapp',
                ],
                [
                    'user_id' => $assignedSales->id,
                    'activity_date' => now()->subDays(rand(1, 5)),
                    'result' => 'Sudah mengirimkan e-brochure & simulasi cicilan KPR.',
                    'notes' => 'Konsumen merespons ramah dan tertarik survei lokasi akhir pekan.',
                    'next_follow_up_date' => now()->addDays(2),
                ]
            );
        }
    }
}
