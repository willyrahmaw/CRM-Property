<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

use App\Http\Requests\Inventory\UpdateProjectRequest;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::withCount(['clusters', 'propertyUnits'])
            ->latest()
            ->paginate(10);

        return view('inventory.projects.index', [
            'projects' => $projects,
        ]);
    }

    public function create(): View
    {
        return view('inventory.projects.create', [
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        $validated['company_id'] = $user->company_id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(4));

        if ($request->hasFile('image')) {
            $path = app(\App\Services\ImageOptimizerService::class)->convertToWebp($request->file('image'), 'properties/projects');
            $validated['image'] = $path;
        }

        $project = Project::create($validated);

        return redirect()
            ->route('inventory.projects.show', $project)
            ->with('success', 'Proyek kawasan ' . $project->name . ' berhasil ditambahkan.');
    }

    public function show(Project $project): View
    {
        $project->load(['clusters.propertyUnits', 'propertyTypes']);

        return view('inventory.projects.show', [
            'project' => $project,
        ]);
    }

    public function edit(Project $project): View
    {
        return view('inventory.projects.edit', [
            'project' => $project,
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project, \App\Services\ImageOptimizerService $imageOptimizer): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            $path = $imageOptimizer->convertToWebp($request->file('image'), 'properties/projects');
            $validated['image'] = $path;
        }

        $project->update($validated);

        return redirect()
            ->route('inventory.projects.show', $project)
            ->with('success', 'Data proyek kawasan ' . $project->name . ' berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        // Proteksi integritas data: Cek apakah memiliki unit dengan transaksi aktif
        $activeUnitsCount = $project->propertyUnits()
            ->whereIn('status', [
                \App\Enums\PropertyUnitStatus::BOOKED,
                \App\Enums\PropertyUnitStatus::RESERVED,
                \App\Enums\PropertyUnitStatus::SOLD,
            ])
            ->count();

        if ($activeUnitsCount > 0) {
            return redirect()
                ->back()
                ->with('error', 'Proyek tidak dapat dihapus karena memiliki ' . $activeUnitsCount . ' unit yang sedang dalam transaksi aktif atau sudah terjual.');
        }

        $name = $project->name;
        $project->delete();

        return redirect()
            ->route('inventory.projects.index')
            ->with('success', 'Proyek kawasan ' . $name . ' berhasil dihapus dari inventori.');
    }
}

