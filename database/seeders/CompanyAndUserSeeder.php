<?php

namespace Database\Seeders;

use App\Enums\IndonesianTimezone;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanyAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Developer Company with Luxury Profile & Settings
        $company = Company::updateOrCreate(
            ['code' => 'GH-LAND'],
            [
                'name' => 'PT Grand Harmony Land',
                'email' => 'contact@grandharmony.co.id',
                'phone' => '021-55889900',
                'address' => 'Grand Harmony Tower Lt. 18, Jl. Boulevard Barat No. 88, BSD City',
                'timezone' => IndonesianTimezone::WIB->value,
                'commission_settings' => [
                    'total_rate' => 2.5,
                    'sales_share' => 60,
                    'team_leader_share' => 20,
                    'agency_share' => 20,
                    'auto_generate_on_booking_fee' => true,
                    'disbursement_policy' => 'after_booking_fee',
                ],
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

        // 2. Default standard secure password: CrmProperty123!
        $defaultPassword = Hash::make('CrmProperty123!');

        // Company Owner
        User::updateOrCreate(
            ['email' => 'owner@propflow.local'],
            [
                'company_id' => $company->id,
                'name' => 'Bambang Wijaya',
                'phone' => '081122334455',
                'password' => $defaultPassword,
                'role' => UserRole::COMPANY_OWNER,
                'timezone' => IndonesianTimezone::WIB->value,
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '8820192837',
                'bank_account_holder' => 'Bambang Wijaya',
                'is_active' => true,
            ]
        );

        // Sales Manager
        User::updateOrCreate(
            ['email' => 'manager@propflow.local'],
            [
                'company_id' => $company->id,
                'name' => 'Hendrik Pratama',
                'phone' => '081233445566',
                'password' => $defaultPassword,
                'role' => UserRole::SALES_MANAGER,
                'timezone' => IndonesianTimezone::WIB->value,
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1370019283746',
                'bank_account_holder' => 'Hendrik Pratama',
                'is_active' => true,
            ]
        );

        // Finance
        User::updateOrCreate(
            ['email' => 'finance@propflow.local'],
            [
                'company_id' => $company->id,
                'name' => 'Siti Rahmawati',
                'phone' => '081344556677',
                'password' => $defaultPassword,
                'role' => UserRole::FINANCE,
                'timezone' => IndonesianTimezone::WIB->value,
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '5420918273',
                'bank_account_holder' => 'Siti Rahmawati',
                'is_active' => true,
            ]
        );

        // Property Admin
        User::updateOrCreate(
            ['email' => 'admin@propflow.local'],
            [
                'company_id' => $company->id,
                'name' => 'Dedi Irawan',
                'phone' => '081455667788',
                'password' => $defaultPassword,
                'role' => UserRole::ADMIN_PROPERTY,
                'timezone' => IndonesianTimezone::WIB->value,
                'bank_name' => 'Bank BNI',
                'bank_account_number' => '0839201928',
                'bank_account_holder' => 'Dedi Irawan',
                'is_active' => true,
            ]
        );

        // Team Leader
        User::updateOrCreate(
            ['email' => 'fajar@propflow.local'],
            [
                'company_id' => $company->id,
                'name' => 'Fajar Nugroho',
                'phone' => '081900112233',
                'password' => $defaultPassword,
                'role' => UserRole::TEAM_LEADER,
                'timezone' => IndonesianTimezone::WIB->value,
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '7120394857',
                'bank_account_holder' => 'Fajar Nugroho',
                'is_active' => true,
            ]
        );

        // Sales Agents
        $salesAgents = [
            [
                'name' => 'Andi Setiawan',
                'email' => 'andi@propflow.local',
                'phone' => '081566778899',
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '8210394857',
                'bank_account_holder' => 'Andi Setiawan',
            ],
            [
                'name' => 'Rina Melati',
                'email' => 'rina@propflow.local',
                'phone' => '081677889900',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1420019283741',
                'bank_account_holder' => 'Rina Melati',
            ],
            [
                'name' => 'Dimas Saputra',
                'email' => 'dimas@propflow.local',
                'phone' => '081788990011',
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '6340291823',
                'bank_account_holder' => 'Dimas Saputra',
            ],
            [
                'name' => 'Maya Anggraini',
                'email' => 'maya@propflow.local',
                'phone' => '081899001122',
                'bank_name' => 'Bank BRI',
                'bank_account_number' => '034101002938531',
                'bank_account_holder' => 'Maya Anggraini',
            ],
        ];

        foreach ($salesAgents as $agent) {
            User::updateOrCreate(
                ['email' => $agent['email']],
                [
                    'company_id' => $company->id,
                    'name' => $agent['name'],
                    'phone' => $agent['phone'],
                    'password' => $defaultPassword,
                    'role' => UserRole::SALES_AGENT,
                    'timezone' => IndonesianTimezone::WIB->value,
                    'bank_name' => $agent['bank_name'],
                    'bank_account_number' => $agent['bank_account_number'],
                    'bank_account_holder' => $agent['bank_account_holder'],
                    'is_active' => true,
                ]
            );
        }
    }
}
