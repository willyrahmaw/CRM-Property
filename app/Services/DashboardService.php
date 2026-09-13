<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Enums\PropertyUnitStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get aggregate dashboard metrics tailored for the active user's role.
     */
    public function getMetricsForUser(User $user): array
    {
        if ($user->isSalesAgent()) {
            return $this->getSalesAgentMetrics($user);
        }

        if ($user->isSalesManager() || $user->isTeamLeader()) {
            return $this->getSalesManagerMetrics($user);
        }

        // Owner / Super Admin / Finance / Admin Property
        return $this->getExecutiveMetrics($user);
    }

    /**
     * Executive / Owner Dashboard Metrics.
     */
    public function getExecutiveMetrics(User $user): array
    {
        $companyId = $user->company_id;

        $totalLeads = Lead::where('company_id', $companyId)->count();
        $hotLeads = Lead::where('company_id', $companyId)->where('temperature', LeadTemperature::HOT)->count();
        $totalSiteVisits = SiteVisit::whereHas('lead', fn($q) => $q->where('company_id', $companyId))->count();
        $totalBookings = Booking::where('company_id', $companyId)->count();
        $approvedBookings = Booking::where('company_id', $companyId)->where('status', BookingStatus::APPROVED)->count();

        // Total sales revenue from approved bookings
        $salesValue = (float) Booking::where('company_id', $companyId)
            ->where('status', BookingStatus::APPROVED)
            ->sum('final_price');

        // Total cash received
        $cashCollected = (float) Payment::whereHas('booking', fn($q) => $q->where('company_id', $companyId))
            ->where('status', \App\Enums\PaymentStatus::VERIFIED)
            ->sum('amount');

        // Unit Inventory Statuses
        $unitCounts = PropertyUnit::whereHas('cluster.project', fn($q) => $q->where('company_id', $companyId))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $availableUnits = $unitCounts[PropertyUnitStatus::AVAILABLE->value] ?? 0;
        $bookedUnits = $unitCounts[PropertyUnitStatus::BOOKED->value] ?? 0;
        $soldUnits = $unitCounts[PropertyUnitStatus::SOLD->value] ?? 0;

        // Pipeline Stages breakdown
        $pipelineStages = Lead::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Conversion Rate: (Approved Bookings / Total Leads) * 100
        $conversionRate = $totalLeads > 0 ? round(($approvedBookings / $totalLeads) * 100, 1) : 0;

        // Top Sales Agent by closed bookings
        $topSales = User::where('company_id', $companyId)
            ->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->withCount(['assignedLeads' => function ($q) {
                $q->where('status', LeadStatus::WON);
            }])
            ->orderBy('assigned_leads_count', 'desc')
            ->take(5)
            ->get();

        return [
            'total_leads' => $totalLeads,
            'hot_leads' => $hotLeads,
            'total_site_visits' => $totalSiteVisits,
            'total_bookings' => $totalBookings,
            'approved_bookings' => $approvedBookings,
            'sales_value' => $salesValue,
            'cash_collected' => $cashCollected,
            'available_units' => $availableUnits,
            'booked_units' => $bookedUnits,
            'sold_units' => $soldUnits,
            'conversion_rate' => $conversionRate,
            'pipeline_stages' => $pipelineStages,
            'top_sales' => $topSales,
        ];
    }

    /**
     * Sales Manager Dashboard Metrics.
     */
    public function getSalesManagerMetrics(User $user): array
    {
        $companyId = $user->company_id;

        $teamMembers = User::where('company_id', $companyId)
            ->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->withCount([
                'assignedLeads',
                'assignedLeads as hot_leads_count' => fn($q) => $q->where('temperature', LeadTemperature::HOT),
                'assignedLeads as closed_leads_count' => fn($q) => $q->where('status', LeadStatus::WON),
            ])
            ->get();

        $unfollowedLeads = Lead::where('company_id', $companyId)
            ->where('status', LeadStatus::NEW)
            ->whereDoesntHave('activities')
            ->count();

        return [
            'team_members' => $teamMembers,
            'unfollowed_leads' => $unfollowedLeads,
            'total_team_leads' => Lead::where('company_id', $companyId)->count(),
            'upcoming_visits' => SiteVisit::whereHas('lead', fn($q) => $q->where('company_id', $companyId))
                ->where('visit_date', '>=', now())
                ->orderBy('visit_date', 'asc')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Sales Agent Individual Metrics.
     */
    public function getSalesAgentMetrics(User $user): array
    {
        $myLeadsCount = Lead::where('assigned_sales_id', $user->id)->count();
        $myHotLeads = Lead::where('assigned_sales_id', $user->id)->where('temperature', LeadTemperature::HOT)->count();

        $todayFollowUps = LeadActivity::where('user_id', $user->id)
            ->whereDate('next_follow_up_date', today())
            ->count();

        $upcomingVisits = SiteVisit::where('user_id', $user->id)
            ->where('visit_date', '>=', now())
            ->orderBy('visit_date', 'asc')
            ->take(5)
            ->get();

        $myBookingsCount = Booking::where('sales_id', $user->id)->where('status', BookingStatus::APPROVED)->count();

        $myEarnedCommission = (float) Commission::where('user_id', $user->id)
            ->whereIn('status', [CommissionStatus::APPROVED, CommissionStatus::PAID])
            ->sum('amount');

        return [
            'my_leads_count' => $myLeadsCount,
            'my_hot_leads' => $myHotLeads,
            'today_follow_ups' => $todayFollowUps,
            'upcoming_visits' => $upcomingVisits,
            'my_bookings_count' => $myBookingsCount,
            'my_earned_commission' => $myEarnedCommission,
        ];
    }
}
