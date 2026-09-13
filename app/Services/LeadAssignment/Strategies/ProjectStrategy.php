<?php

namespace App\Services\LeadAssignment\Strategies;

use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadAssignment\Contracts\LeadAssignmentStrategy;

class ProjectStrategy implements LeadAssignmentStrategy
{
    protected RoundRobinStrategy $fallback;

    public function __construct()
    {
        $this->fallback = new RoundRobinStrategy();
    }

    public function assign(Lead $lead, array $context = []): ?User
    {
        // When specific project assignment is configured, filter agents; fallback to round-robin
        return $this->fallback->assign($lead, $context);
    }

    public function name(): string
    {
        return 'Project Specialized';
    }
}
