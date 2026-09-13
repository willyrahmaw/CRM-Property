<?php

namespace Database\Seeders;

use App\Enums\NegotiationApprovalStatus;
use App\Models\Lead;
use App\Models\Negotiation;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class NegotiationSeeder extends Seeder
{
    public function run(): void
    {
        $leads = Lead::all();
        $units = PropertyUnit::all();
        $sales = User::where('role', 'sales_agent')->first() ?? User::first();
        $manager = User::where('role', 'sales_manager')->first() ?? User::first();

        if (Negotiation::count() === 0 && $leads->count() >= 3 && $units->count() >= 3) {
            Negotiation::create([
                'lead_id' => $leads[0]->id,
                'property_unit_id' => $units[0]->id,
                'sales_id' => $sales->id,
                'initial_price' => $units[0]->selling_price,
                'customer_offer_price' => $units[0]->selling_price - 25000000,
                'final_price' => $units[0]->selling_price - 25000000,
                'discount_amount' => 25000000,
                'discount_percentage' => round((25000000 / $units[0]->selling_price) * 100, 2),
                'promo_description' => 'Promo Spesial Gathering Akhir Pekan',
                'approval_status' => NegotiationApprovalStatus::PENDING,
                'notes' => 'Konsumen siap bayar booking fee hari ini jika diskon 25jt disetujui.',
            ]);

            Negotiation::create([
                'lead_id' => $leads[1]->id,
                'property_unit_id' => $units[1]->id,
                'sales_id' => $sales->id,
                'initial_price' => $units[1]->selling_price,
                'customer_offer_price' => $units[1]->selling_price - 15000000,
                'final_price' => $units[1]->selling_price - 15000000,
                'discount_amount' => 15000000,
                'discount_percentage' => round((15000000 / $units[1]->selling_price) * 100, 2),
                'promo_description' => 'Subsidi DP & Biaya Notaris',
                'approval_status' => NegotiationApprovalStatus::APPROVED,
                'approved_by_id' => $manager->id,
                'notes' => 'Disetujui manajer untuk percepatan penutupan penjualan kaveling boulevard.',
            ]);

            Negotiation::create([
                'lead_id' => $leads[2]->id,
                'property_unit_id' => $units[2]->id,
                'sales_id' => $sales->id,
                'initial_price' => $units[2]->selling_price,
                'customer_offer_price' => $units[2]->selling_price - 80000000,
                'final_price' => $units[2]->selling_price - 80000000,
                'discount_amount' => 80000000,
                'discount_percentage' => round((80000000 / $units[2]->selling_price) * 100, 2),
                'promo_description' => 'Permintaan Diskon Cash Keras',
                'approval_status' => NegotiationApprovalStatus::REJECTED,
                'approved_by_id' => $manager->id,
                'notes' => 'Ditolak: Diskon 80jt melebihi margin batas toleransi promo developer (maks 30jt).',
            ]);
        }
    }
}
