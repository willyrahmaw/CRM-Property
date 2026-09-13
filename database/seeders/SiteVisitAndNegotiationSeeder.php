<?php

namespace Database\Seeders;

use App\Enums\NegotiationApprovalStatus;
use App\Enums\SiteVisitStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Negotiation;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Database\Seeder;

class SiteVisitAndNegotiationSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();
        $project = Project::firstOrFail();
        $manager = User::where('company_id', $company->id)->where('role', UserRole::SALES_MANAGER)->firstOrFail();
        $salesAgents = User::where('company_id', $company->id)->where('role', UserRole::SALES_AGENT)->get();
        $leads = Lead::where('company_id', $company->id)->get();
        $units = PropertyUnit::all();

        // 1. Create 5 Site Visits
        if ($leads->count() >= 20 && $units->count() >= 5) {
            for ($v = 0; $v < 5; $v++) {
                $targetLead = $leads[$v + 15];
                $targetUnit = $units[$v];
                $sales = $salesAgents[$v % $salesAgents->count()];

                SiteVisit::updateOrCreate(
                    [
                        'lead_id' => $targetLead->id,
                        'project_id' => $project->id,
                    ],
                    [
                        'user_id' => $sales->id,
                        'property_unit_id' => $targetUnit->id,
                        'visit_date' => now()->addDays($v + 1)->setTime(10 + $v, 0),
                        'status' => SiteVisitStatus::SCHEDULED,
                        'notes' => 'Konsumen ingin melihat langsung show unit tipe 45 dan lingkungan cluster.',
                    ]
                );
            }
        }

        // 2. Create 3 Realistic Negotiations & Discounts
        if ($leads->count() >= 10 && $units->count() >= 10) {
            // Negotiation 1: Pending
            Negotiation::updateOrCreate(
                [
                    'lead_id' => $leads[2]->id,
                    'property_unit_id' => $units[7]->id,
                ],
                [
                    'sales_id' => $salesAgents[0]->id,
                    'initial_price' => $units[7]->selling_price,
                    'customer_offer_price' => $units[7]->selling_price - 25000000,
                    'final_price' => $units[7]->selling_price - 25000000,
                    'discount_amount' => 25000000,
                    'discount_percentage' => round((25000000 / $units[7]->selling_price) * 100, 2),
                    'promo_description' => 'Promo Spesial Gathering Akhir Pekan',
                    'approval_status' => NegotiationApprovalStatus::PENDING,
                    'notes' => 'Konsumen siap bayar booking fee hari ini jika diskon 25jt disetujui.',
                ]
            );

            // Negotiation 2: Approved
            Negotiation::updateOrCreate(
                [
                    'lead_id' => $leads[5]->id,
                    'property_unit_id' => $units[8]->id,
                ],
                [
                    'sales_id' => $salesAgents[1 % $salesAgents->count()]->id,
                    'initial_price' => $units[8]->selling_price,
                    'customer_offer_price' => $units[8]->selling_price - 15000000,
                    'final_price' => $units[8]->selling_price - 15000000,
                    'discount_amount' => 15000000,
                    'discount_percentage' => round((15000000 / $units[8]->selling_price) * 100, 2),
                    'promo_description' => 'Subsidi DP & Biaya Notaris',
                    'approval_status' => NegotiationApprovalStatus::APPROVED,
                    'approved_by_id' => $manager->id,
                    'notes' => 'Disetujui manajer untuk percepatan penutupan penjualan kaveling boulevard.',
                ]
            );

            // Negotiation 3: Rejected
            Negotiation::updateOrCreate(
                [
                    'lead_id' => $leads[8]->id,
                    'property_unit_id' => $units[9]->id,
                ],
                [
                    'sales_id' => $salesAgents[2 % $salesAgents->count()]->id,
                    'initial_price' => $units[9]->selling_price,
                    'customer_offer_price' => $units[9]->selling_price - 80000000,
                    'final_price' => $units[9]->selling_price - 80000000,
                    'discount_amount' => 80000000,
                    'discount_percentage' => round((80000000 / $units[9]->selling_price) * 100, 2),
                    'promo_description' => 'Permintaan Diskon Cash Keras',
                    'approval_status' => NegotiationApprovalStatus::REJECTED,
                    'approved_by_id' => $manager->id,
                    'notes' => 'Ditolak: Diskon 80jt melebihi margin batas toleransi promo developer (maks 30jt).',
                ]
            );
        }
    }
}
