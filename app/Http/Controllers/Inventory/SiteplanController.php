<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\PropertyUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteplanController extends Controller
{
    public function index(Request $request): View
    {
        $project = Project::with(['clusters.propertyUnits.propertyType'])->first();

        if (!$project) {
            abort(404, 'Belum ada project properti yang terdaftar.');
        }

        $allProjects = Project::orderBy('name')->get(['id', 'name']);

        return view('inventory.siteplan.index', [
            'project' => $project,
            'allProjects' => $allProjects,
        ]);
    }

    public function show(Project $project): View
    {
        $project->load(['clusters.propertyUnits.propertyType', 'clusters.propertyUnits.bookings.customer']);
        $allProjects = Project::orderBy('name')->get(['id', 'name']);

        return view('inventory.siteplan.index', [
            'project' => $project,
            'allProjects' => $allProjects,
        ]);
    }

    public function getUnitDetail(PropertyUnit $unit): JsonResponse
    {
        $unit->load(['cluster.project', 'propertyType', 'bookings.customer', 'bookings.sales']);

        $latestBooking = $unit->bookings->first();

        return response()->json([
            'id' => $unit->id,
            'unit_number' => $unit->unit_number,
            'block' => $unit->block,
            'cluster_name' => $unit->cluster->name,
            'type_name' => $unit->propertyType?->name ?? 'Standard',
            'building_area' => (float) $unit->building_area,
            'land_area' => (float) $unit->land_area,
            'bedrooms' => $unit->bedrooms,
            'bathrooms' => $unit->bathrooms,
            'selling_price' => (float) $unit->selling_price,
            'formatted_price' => 'Rp ' . number_format((float) $unit->selling_price, 0, ',', '.'),
            'status' => $unit->status->value,
            'status_label' => $unit->status->label(),
            'status_color' => $unit->status->colorHex(),
            'customer_name' => $latestBooking?->customer?->name,
            'sales_name' => $latestBooking?->sales?->name,
            'booking_number' => $latestBooking?->booking_number,
        ]);
    }
}
