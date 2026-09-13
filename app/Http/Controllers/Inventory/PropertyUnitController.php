<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\PropertyUnitStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePropertyUnitRequest;
use App\Models\Cluster;
use App\Models\Project;
use App\Models\PropertyType;
use App\Models\PropertyUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyUnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyUnit::with(['cluster.project', 'propertyType'])->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cluster_id')) {
            $query->where('cluster_id', $request->cluster_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unit_number', 'like', "%{$search}%")
                  ->orWhere('block', 'like', "%{$search}%");
            });
        }

        $units = $query->paginate(20)->withQueryString();
        $clusters = Cluster::orderBy('name')->get(['id', 'name']);

        return view('inventory.units.index', [
            'units' => $units,
            'clusters' => $clusters,
            'statuses' => PropertyUnitStatus::cases(),
            'filters' => $request->all(),
        ]);
    }

    public function create(): View
    {
        $clusters = Cluster::with('project')->orderBy('name')->get();
        $propertyTypes = PropertyType::with('project')->orderBy('name')->get();

        return view('inventory.units.create', [
            'clusters' => $clusters,
            'propertyTypes' => $propertyTypes,
        ]);
    }

    public function store(StorePropertyUnitRequest $request, \App\Services\ImageOptimizerService $imageOptimizer): RedirectResponse
    {
        $validated = $request->validated();
        $validated['base_price'] = $validated['base_price'] ?? $validated['selling_price'];
        $validated['status'] = PropertyUnitStatus::AVAILABLE;

        if ($request->hasFile('image')) {
            $path = $imageOptimizer->convertToWebp($request->file('image'), 'properties/units');
            $validated['image'] = $path;
        }

        $unit = PropertyUnit::create($validated);

        return redirect()
            ->route('inventory.units.index')
            ->with('success', 'Unit properti ' . $unit->unit_number . ' berhasil ditambahkan ke inventori.');
    }

    public function show(PropertyUnit $unit): View
    {
        $unit->load([
            'cluster.project',
            'propertyType',
            'bookings.customer',
            'bookings.sales',
            'siteVisits.sales',
            'siteVisits.lead',
        ]);

        return view('inventory.units.show', [
            'unit' => $unit,
        ]);
    }
}
