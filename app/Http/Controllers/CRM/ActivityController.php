<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreActivityRequest;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\LeadScoringService;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{
    public function __construct(
        protected LeadScoringService $scoringService
    ) {}

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $activity = LeadActivity::create($validated);

        // Recalculate lead scoring
        $this->scoringService->recalculate($activity->lead);

        return back()->with('success', 'Catatan aktivitas follow-up berhasil direkam.');
    }
}
