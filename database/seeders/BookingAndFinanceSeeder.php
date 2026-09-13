<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\MortgageStatus;
use App\Enums\PaymentScheme;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PropertyUnitStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Mortgage;
use App\Models\Payment;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingAndFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();
        $owner = User::where('company_id', $company->id)->where('role', UserRole::COMPANY_OWNER)->firstOrFail();
        $finance = User::where('company_id', $company->id)->where('role', UserRole::FINANCE)->firstOrFail();
        $teamLeader = User::where('company_id', $company->id)->where('role', UserRole::TEAM_LEADER)->firstOrFail();
        $salesAgents = User::where('company_id', $company->id)->where('role', UserRole::SALES_AGENT)->get();

        $bookedUnits = PropertyUnit::whereIn('status', [PropertyUnitStatus::BOOKED, PropertyUnitStatus::SOLD])
            ->take(6)
            ->get();

        $customers = Customer::where('company_id', $company->id)->take(6)->get();

        foreach ($bookedUnits as $b => $targetUnit) {
            $customer = $customers[$b % $customers->count()];
            $sales = $salesAgents[$b % $salesAgents->count()];
            $bookingNumber = sprintf('BKG-202603-%04d', $b + 1);

            $booking = Booking::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'booking_number' => $bookingNumber,
                ],
                [
                    'customer_id' => $customer->id,
                    'property_unit_id' => $targetUnit->id,
                    'sales_id' => $sales->id,
                    'booking_date' => now()->subDays(10 - $b)->toDateString(),
                    'booking_fee' => 10000000,
                    'unit_price' => $targetUnit->selling_price,
                    'discount_amount' => 10000000,
                    'final_price' => $targetUnit->selling_price - 10000000,
                    'payment_scheme' => ($b % 2 === 0) ? PaymentScheme::KPR : PaymentScheme::CASH_BERTAHAP,
                    'status' => ($b < 4) ? BookingStatus::APPROVED : BookingStatus::PENDING,
                    'approved_by_id' => ($b < 4) ? $owner->id : null,
                    'notes' => 'Surat pesanan resmi telah ditandatangani konsumen.',
                ]
            );

            // Create Verified Payment for booking fee
            $paymentNumber = sprintf('PAY-202603-%04d', $b + 1);
            Payment::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'payment_number' => $paymentNumber,
                ],
                [
                    'verified_by_id' => $finance->id,
                    'payment_type' => PaymentType::BOOKING_FEE,
                    'amount' => 10000000,
                    'payment_date' => now()->subDays(9 - $b)->toDateString(),
                    'payment_method' => 'Transfer Bank BCA',
                    'reference_number' => 'TRX-' . (100000 + $b * 5432),
                    'status' => PaymentStatus::VERIFIED,
                    'verified_at' => now()->subDays(8 - $b),
                    'notes' => 'Dana masuk rekening BCA PT Grand Harmony Land.',
                ]
            );

            // Seed Mortgage record if payment scheme is KPR
            if ($booking->payment_scheme === PaymentScheme::KPR) {
                $banks = ['Bank Mandiri', 'Bank Central Asia (BCA)', 'Bank BTN (Persero)'];
                $bankName = $banks[$b % count($banks)];
                $submissionAmount = round($booking->final_price * 0.85); // 85% plafon
                $approvedAmount = ($b === 0 || $b === 2) ? $submissionAmount : null;
                $status = match ($b) {
                    0 => MortgageStatus::CONTRACT_SIGNED, // Akad Kredit Selesai
                    2 => MortgageStatus::APPROVED,        // SP3K Disetujui
                    default => MortgageStatus::APPRAISAL, // Sedang Penilaian Bank
                };

                Mortgage::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'bank_name' => $bankName,
                        'submission_amount' => $submissionAmount,
                        'approved_amount' => $approvedAmount,
                        'tenor_years' => 15,
                        'interest_rate' => 6.25,
                        'estimated_installment' => round($submissionAmount * 0.0085),
                        'status' => $status,
                        'application_date' => now()->subDays(15)->toDateString(),
                        'appraisal_date' => now()->subDays(10)->toDateString(),
                        'sp3k_date' => ($b === 0 || $b === 2) ? now()->subDays(5)->toDateString() : null,
                        'contract_date' => ($b === 0) ? now()->subDays(2)->toDateString() : null,
                        'notes' => ($b === 0) ? 'Akad kredit selesai dilaksanakan di hadapan Notaris & Bank.' : 'Proses berkas lancar, appraisal sudah selesai.',
                    ]
                );
            }

            // Generate Multi-Tier Commissions
            $totalComm = ($booking->final_price * 2.5) / 100;

            // 1. Sales Closing Commission (60% share = 1.5% of price)
            Commission::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'user_id' => $sales->id,
                    'beneficiary_type' => 'sales',
                ],
                [
                    'selling_price' => $booking->final_price,
                    'percentage' => 1.5,
                    'amount' => ($totalComm * 0.60),
                    'status' => ($b < 2) ? CommissionStatus::PAID : CommissionStatus::APPROVED,
                    'approved_by_id' => $finance->id,
                    'paid_at' => ($b < 2) ? now()->subDays(2)->toDateString() : null,
                ]
            );

            // 2. Team Leader Commission (20% share = 0.5% of price)
            Commission::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'user_id' => $teamLeader->id,
                    'beneficiary_type' => 'team_leader',
                ],
                [
                    'selling_price' => $booking->final_price,
                    'percentage' => 0.5,
                    'amount' => ($totalComm * 0.20),
                    'status' => CommissionStatus::APPROVED,
                    'approved_by_id' => $finance->id,
                ]
            );
        }
    }
}
