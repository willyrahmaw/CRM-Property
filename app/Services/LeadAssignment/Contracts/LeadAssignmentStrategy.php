<?php

namespace App\Services\LeadAssignment\Contracts;

use App\Models\Lead;
use App\Models\User;

interface LeadAssignmentStrategy
{
    /**
     * Assign a sales agent to the given lead.
     */
    public function assign(Lead $lead, array $context = []): ?User;

    /**
     * Human-readable name of the strategy.
     */
    public function name(): string;
}
