<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Default commission scheme:
     * Total commission rate = 2.5% of final selling price.
     * Distribution:
     * - Sales Closing: 60% of total commission (1.50% of price)
     * - Team Leader: 20% of total commission (0.50% of price)
     * - Agency/Company: 20% of total commission (0.50% of price)
     */
    public function calculateAndGenerate(
        Booking $booking,
        ?float $totalCommissionRate = null,
        ?float $salesShare = null,
        ?float $teamLeaderShare = null,
        ?float $agencyShare = null,
        bool $force = false
    ): Collection {
        return DB::transaction(function () use (
            $booking,
            $totalCommissionRate,
            $salesShare,
            $teamLeaderShare,
            $agencyShare,
            $force
        ) {
            $company = $booking->company;
            $rate = $totalCommissionRate ?? (float) ($company?->getCommissionSetting('total_rate', 2.5) ?? 2.5);
            $sShare = $salesShare ?? (float) ($company?->getCommissionSetting('sales_share', 60.0) ?? 60.0);
            $tlShare = $teamLeaderShare ?? (float) ($company?->getCommissionSetting('team_leader_share', 20.0) ?? 20.0);
            $agShare = $agencyShare ?? (float) ($company?->getCommissionSetting('agency_share', 20.0) ?? 20.0);

            if ($booking->commissions()->exists()) {
                $booking->commissions()->delete();
            }

            $finalPrice = (float) $booking->final_price;
            $totalCommissionAmount = ($finalPrice * $rate) / 100;

            $commissions = collect();

            // 1. Sales Closing Share
            $salesAmount = ($totalCommissionAmount * $sShare) / 100;
            $salesComm = Commission::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->sales_id,
                'beneficiary_type' => 'sales',
                'selling_price' => $finalPrice,
                'percentage' => round(($rate * $sShare) / 100, 2),
                'amount' => $salesAmount,
                'status' => CommissionStatus::PENDING,
            ]);
            $commissions->push($salesComm);

            // 2. Team Leader Share (if company owner/manager exists)
            $teamLeader = User::query()
                ->where('company_id', $booking->company_id)
                ->where('role', \App\Enums\UserRole::TEAM_LEADER)
                ->first() ?? $booking->sales; // Fallback to sales if no TL

            $tlAmount = ($totalCommissionAmount * $tlShare) / 100;
            $tlComm = Commission::create([
                'booking_id' => $booking->id,
                'user_id' => $teamLeader->id,
                'beneficiary_type' => 'team_leader',
                'selling_price' => $finalPrice,
                'percentage' => round(($rate * $tlShare) / 100, 2),
                'amount' => $tlAmount,
                'status' => CommissionStatus::PENDING,
            ]);
            $commissions->push($tlComm);

            $this->auditLogService->log(
                action: 'commissions_generated',
                entity: $booking,
                before: null,
                after: [
                    'booking_id' => $booking->id,
                    'rate' => $rate,
                    'total_commission' => $totalCommissionAmount,
                    'sales_amount' => $salesAmount,
                    'tl_amount' => $tlAmount,
                ]
            );

            return $commissions;
        });
    }

    /**
     * Approve a pending commission.
     */
    public function approve(Commission $commission, User $approver): Commission
    {
        return DB::transaction(function () use ($commission, $approver) {
            $commission->update([
                'status' => CommissionStatus::APPROVED,
                'approved_by_id' => $approver->id,
            ]);

            $this->auditLogService->log(
                action: 'commission_approved',
                entity: $commission,
                before: ['status' => CommissionStatus::PENDING->value],
                after: ['status' => CommissionStatus::APPROVED->value, 'approver' => $approver->name]
            );

            return $commission;
        });
    }

    /**
     * Mark commission as paid.
     */
    public function markAsPaid(Commission $commission, User $payer): Commission
    {
        return DB::transaction(function () use ($commission, $payer) {
            $commission->update([
                'status' => CommissionStatus::PAID,
                'paid_at' => now()->toDateString(),
            ]);

            $this->auditLogService->log(
                action: 'commission_paid',
                entity: $commission,
                before: ['status' => $commission->status->value],
                after: ['status' => CommissionStatus::PAID->value, 'paid_at' => now()->toDateString()]
            );

            return $commission;
        });
    }
}
