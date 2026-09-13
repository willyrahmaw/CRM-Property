<?php

namespace App\Services\LeadAssignment\Strategies;

use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadAssignment\Contracts\LeadAssignmentStrategy;

class RoundRobinStrategy implements LeadAssignmentStrategy
{
    public function assign(Lead $lead, array $context = []): ?User
    {
        // Query active sales agents within the same company
        $salesAgents = User::query()
            ->where('company_id', $lead->company_id)
            ->where('is_active', true)
            ->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->get();

        if ($salesAgents->isEmpty()) {
            return null;
        }

        // Find the sales agent who hasn't received a lead the longest
        return $salesAgents->sortBy(function (User $user) {
            $latestLead = Lead::withoutGlobalScopes()
                ->where('assigned_sales_id', $user->id)
                ->latest('created_at')
                ->first();

            return $latestLead ? $latestLead->created_at->timestamp : 0;
        })->first();
    }

    public function name(): string
    {
        return 'Round Robin';
    }
}
