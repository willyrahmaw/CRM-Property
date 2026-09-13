<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display executive or role-specific analytics dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $metrics = $this->dashboardService->getMetricsForUser($user);

        return view('dashboard.index', [
            'metrics' => $metrics,
            'user' => $user,
        ]);
    }
}
