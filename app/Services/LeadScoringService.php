<?php

namespace App\Services;

use App\Enums\LeadTemperature;
use App\Models\Lead;

class LeadScoringService
{
    /**
     * Default scoring weights.
     */
    protected array $weights = [
        'budget_matched' => 15,
        'purchase_target_under_30_days' => 20,
        'has_site_visit' => 20,
        'requested_mortgage_simulation' => 15,
        'responsive_reply' => 10,
        'multiple_followups' => 10,
        'has_email_and_phone' => 10,
    ];

    /**
     * Calculate and update score and temperature for the lead.
     */
    public function recalculate(Lead $lead): array
    {
        $score = 0;

        // 1. Budget Match: Budget min > 0 and budget max >= budget min
        if ($lead->budget_max > 0 && $lead->budget_max >= $lead->budget_min) {
            $score += $this->weights['budget_matched'];
        }

        // 2. Target purchase timeframe
        if ($lead->purchase_target_days !== null && $lead->purchase_target_days <= 30) {
            $score += $this->weights['purchase_target_under_30_days'];
        }

        // 3. Has site visits
        if ($lead->siteVisits()->exists()) {
            $score += $this->weights['has_site_visit'];
        }

        // 4. Repeated followups (more than 2 activities)
        $activityCount = $lead->activities()->count();
        if ($activityCount >= 3) {
            $score += $this->weights['multiple_followups'];
        } elseif ($activityCount >= 1) {
            $score += $this->weights['responsive_reply'];
        }

        // 5. Complete contact credentials
        if (!empty($lead->email) && !empty($lead->phone)) {
            $score += $this->weights['has_email_and_phone'];
        }

        // Cap at 100
        $finalScore = min(100, max(0, $score));

        // Determine temperature
        $temperature = match (true) {
            $finalScore >= 70 => LeadTemperature::HOT,
            $finalScore >= 40 => LeadTemperature::WARM,
            default => LeadTemperature::COLD,
        };

        $lead->update([
            'score' => $finalScore,
            'temperature' => $temperature,
        ]);

        return [
            'score' => $finalScore,
            'temperature' => $temperature,
        ];
    }
}
