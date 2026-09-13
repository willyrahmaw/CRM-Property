<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\PropertyUnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Cluster;
use App\Models\Project;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Http\Requests\Inventory\StoreClusterRequest;
use App\Http\Requests\Inventory\UpdateClusterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ClusterController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cluster::with('project')
            ->withCount([
                'propertyUnits',
                'propertyUnits as available_units_count' => function ($q) {
                    $q->where('status', PropertyUnitStatus::AVAILABLE);
                },
                'propertyUnits as booked_units_count' => function ($q) {
                    $q->where('status', PropertyUnitStatus::BOOKED);
                },
                'propertyUnits as sold_units_count' => function ($q) {
                    $q->where('status', PropertyUnitStatus::SOLD);
                },
            ])
            ->latest('created_at');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $clusters = $query->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        $metrics = [
            'total_clusters' => Cluster::count(),
            'total_units' => PropertyUnit::count(),
            'available_units' => PropertyUnit::where('status', PropertyUnitStatus::AVAILABLE)->count(),
            'sold_booked_units' => PropertyUnit::whereIn('status', [PropertyUnitStatus::BOOKED, PropertyUnitStatus::SOLD])->count(),
        ];

        return view('inventory.clusters.index', [
            'clusters' => $clusters,
            'projects' => $projects,
            'metrics' => $metrics,
            'filters' => $request->only(['search', 'project_id']),
        ]);
    }

    public function create(Request $request): View
    {
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('inventory.clusters.create', [
            'projects' => $projects,
            'selectedProjectId' => $request->query('project_id'),
        ]);
    }

    public function store(StoreClusterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photoPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('properties/clusters', 'public');
                $photoPaths[] = $path;
            }
        }

        $validated['photos'] = $photoPaths;

        $cluster = Cluster::create($validated);

        return redirect()
            ->route('inventory.clusters.index', ['project_id' => $cluster->project_id])
            ->with('success', 'Cluster ' . $cluster->name . ' berhasil ditambahkan.');
    }

    public function edit(Cluster $cluster): View
    {
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('inventory.clusters.edit', [
            'cluster' => $cluster,
            'projects' => $projects,
        ]);
    }

    public function update(UpdateClusterRequest $request, Cluster $cluster): RedirectResponse
    {
        $validated = $request->validated();
        $currentPhotos = $cluster->photos ?? [];

        // Hapus foto yang dicentang untuk dihapus
        if ($request->filled('delete_photos') && is_array($request->delete_photos)) {
            foreach ($request->delete_photos as $photoToDelete) {
                if (Storage::disk('public')->exists($photoToDelete)) {
                    Storage::disk('public')->delete($photoToDelete);
                }
                $currentPhotos = array_values(array_filter($currentPhotos, fn ($p) => $p !== $photoToDelete));
            }
        }

        // Tambahkan foto-foto baru yang diunggah
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('properties/clusters', 'public');
                $currentPhotos[] = $path;
            }
        }

        $validated['photos'] = $currentPhotos;

        $cluster->update($validated);

        return redirect()
            ->route('inventory.clusters.index', ['project_id' => $cluster->project_id])
            ->with('success', 'Data cluster ' . $cluster->name . ' berhasil diperbarui.');
    }

    public function destroy(Cluster $cluster): RedirectResponse
    {
        // Proteksi integritas: cek apakah unit dalam cluster sedang transaksi aktif
        $activeUnitsCount = $cluster->propertyUnits()
            ->whereIn('status', [
                PropertyUnitStatus::BOOKED,
                PropertyUnitStatus::RESERVED,
                PropertyUnitStatus::SOLD,
            ])
            ->count();

        if ($activeUnitsCount > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cluster tidak dapat dihapus karena memiliki ' . $activeUnitsCount . ' unit yang sedang dalam transaksi aktif atau sudah terjual.');
        }

        $name = $cluster->name;
        $cluster->delete();

        return redirect()
            ->route('inventory.clusters.index')
            ->with('success', 'Cluster ' . $name . ' berhasil dihapus dari sistem.');
    }
}

