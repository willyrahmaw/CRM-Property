<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportingService $reportingService
    ) {}

    /**
     * Display executive sales, conversion, and revenue reports.
     */
    public function sales(Request $request): View
    {
        $reportData = $this->reportingService->getSalesReportData($request->user());

        return view('reports.sales', $reportData);
    }
}
