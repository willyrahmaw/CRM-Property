<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();

        $customerData = [
            ['name' => 'Dr. Irwan Setiadi', 'gender' => Gender::MALE, 'phone' => '081290901111', 'email' => 'irwan.setiadi@gmail.com', 'nik' => '3276011203850001', 'occupation' => 'Dokter Spesialis'],
            ['name' => 'Hj. Nurul Aini', 'gender' => Gender::FEMALE, 'phone' => '081380802222', 'email' => 'nurul.aini@yahoo.com', 'nik' => '3276012408900002', 'occupation' => 'Pengusaha Tekstil'],
            ['name' => 'Kevin Pratama, S.T.', 'gender' => Gender::MALE, 'phone' => '081170703333', 'email' => 'kevin.pratama@tech.co.id', 'nik' => '3174021501930003', 'occupation' => 'Software Architect'],
            ['name' => 'Agus Hartono', 'gender' => Gender::MALE, 'phone' => '081560604444', 'email' => 'agus.hartono@gmail.com', 'nik' => '3201010505820004', 'occupation' => 'Direktur Operasional'],
            ['name' => 'Dewi Lestari', 'gender' => Gender::FEMALE, 'phone' => '081650505555', 'email' => 'dewi.lestari@corporate.id', 'nik' => '3275031907880005', 'occupation' => 'Finance Manager'],
            ['name' => 'Bambang Kusumo', 'gender' => Gender::MALE, 'phone' => '081740406666', 'email' => 'bambang.k@outlook.com', 'nik' => '3171010109790006', 'occupation' => 'Notaris & PPAT'],
            ['name' => 'Ratna Sari', 'gender' => Gender::FEMALE, 'phone' => '081830307777', 'email' => 'ratna.sari@gmail.com', 'nik' => '3276011111870007', 'occupation' => 'Konsultan Bisnis'],
            ['name' => 'Taufik Hidayat', 'gender' => Gender::MALE, 'phone' => '081920208888', 'email' => 'taufik.hidayat@gmail.com', 'nik' => '3201021406840008', 'occupation' => 'Wiraswasta'],
            ['name' => 'Citra Kirana', 'gender' => Gender::FEMALE, 'phone' => '081210109999', 'email' => 'citra.kirana@gmail.com', 'nik' => '3175052203920009', 'occupation' => 'Marketing Director'],
            ['name' => 'Eko Prasetyo', 'gender' => Gender::MALE, 'phone' => '081300001010', 'email' => 'eko.prasetyo@gmail.com', 'nik' => '3275010909860010', 'occupation' => 'Senior Engineer'],
        ];

        foreach ($customerData as $index => $c) {
            Customer::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'phone' => $c['phone'],
                ],
                [
                    'name' => $c['name'],
                    'gender' => $c['gender'],
                    'nik' => $c['nik'],
                    'email' => $c['email'],
                    'address' => 'Jl. Boulevard Raya No. ' . ($index + 1) * 7 . ', BSD City, Tangerang Selatan',
                    'occupation' => $c['occupation'],
                ]
            );
        }
    }
}
