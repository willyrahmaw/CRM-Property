<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\LeadStatus;
use App\Enums\PropertyUnitStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class ReportingService
{
    /**
     * Get aggregate sales report data scoped to user company context.
     *
     * @return array<string, mixed>
     */
    public function getSalesReportData(?User $user): array
    {
        $companyId = $user?->company_id;
        $isSuperAdmin = $user && $user->isSuperAdmin();

        return [
            'projectSummaries' => $this->getProjectSummaries($companyId, $isSuperAdmin),
            'funnel' => $this->getConversionFunnel($companyId, $isSuperAdmin),
            'financials' => $this->getFinancialMetrics($companyId, $isSuperAdmin),
            'salesLeaderboard' => $this->getSalesLeaderboard($companyId, $isSuperAdmin),
        ];
    }

    /**
     * Calculate project stock and unit absorption rates.
     */
    protected function getProjectSummaries(?string $companyId, bool $isSuperAdmin): Collection
    {
        $query = Project::with(['clusters.propertyUnits']);

        if (! $isSuperAdmin && $companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function (Project $project) {
            $allUnits = $project->clusters->flatMap->propertyUnits;
            $totalUnits = $allUnits->count();
            $soldUnits = $allUnits->whereIn('status', [PropertyUnitStatus::SOLD, PropertyUnitStatus::BOOKED])->count();
            $availableUnits = $allUnits->where('status', PropertyUnitStatus::AVAILABLE)->count();
            $absorptionRate = $totalUnits > 0 ? round(($soldUnits / $totalUnits) * 100, 1) : 0;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'location' => $project->location,
                'total_units' => $totalUnits,
                'sold_units' => $soldUnits,
                'available_units' => $availableUnits,
                'absorption_rate' => $absorptionRate,
            ];
        });
    }

    /**
     * Calculate pipeline conversion stages.
     *
     * @return array<string, int>
     */
    protected function getConversionFunnel(?string $companyId, bool $isSuperAdmin): array
    {
        $leadsQuery = Lead::query();
        if (! $isSuperAdmin && $companyId) {
            $leadsQuery->where('company_id', $companyId);
        }

        $total = (clone $leadsQuery)->count();
        $contacted = (clone $leadsQuery)->whereIn('status', [
            LeadStatus::CONTACTED,
            LeadStatus::QUALIFIED,
            LeadStatus::SITE_VISIT,
            LeadStatus::NEGOTIATION,
            LeadStatus::BOOKING,
            LeadStatus::WON,
        ])->count();

        $siteVisit = (clone $leadsQuery)->whereIn('status', [
            LeadStatus::SITE_VISIT,
            LeadStatus::NEGOTIATION,
            LeadStatus::BOOKING,
            LeadStatus::WON,
        ])->count();

        $booking = (clone $leadsQuery)->whereIn('status', [
            LeadStatus::BOOKING,
            LeadStatus::WON,
        ])->count();

        $won = (clone $leadsQuery)->where('status', LeadStatus::WON)->count();

        return [
            'total' => $total,
            'contacted' => $contacted,
            'site_visit' => $siteVisit,
            'booking' => $booking,
            'won' => $won,
        ];
    }

    /**
     * Calculate financial sales revenue and cash inflows.
     *
     * @return array<string, float|int>
     */
    protected function getFinancialMetrics(?string $companyId, bool $isSuperAdmin): array
    {
        $bookingQuery = Booking::query();
        if (! $isSuperAdmin && $companyId) {
            $bookingQuery->where('company_id', $companyId);
        }

        $totalRevenue = (float) (clone $bookingQuery)->where('status', BookingStatus::APPROVED)->sum('final_price');
        $totalBookings = (clone $bookingQuery)->where('status', BookingStatus::APPROVED)->count();

        $paymentQuery = Payment::query();
        if (! $isSuperAdmin && $companyId) {
            $paymentQuery->whereHas('booking', fn ($q) => $q->where('company_id', $companyId));
        }
        $cashCollected = (float) (clone $paymentQuery)->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'cash_collected' => $cashCollected,
        ];
    }

    /**
     * Rank sales agents by sales volume and closing units.
     */
    protected function getSalesLeaderboard(?string $companyId, bool $isSuperAdmin): Collection
    {
        $query = User::whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->withCount(['assignedLeads']);

        if (! $isSuperAdmin && $companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()
            ->map(function (User $agent) {
                $approvedBookings = Booking::where('sales_id', $agent->id)
                    ->where('status', BookingStatus::APPROVED)
                    ->get();

                return [
                    'agent' => $agent,
                    'leads_count' => $agent->assigned_leads_count,
                    'closing_count' => $approvedBookings->count(),
                    'sales_volume' => (float) $approvedBookings->sum('final_price'),
                ];
            })
            ->sortByDesc('sales_volume')
            ->values();
    }
}
