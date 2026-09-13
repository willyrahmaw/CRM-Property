<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display list of users, roles, and access status.
     */
    public function index(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        $query = User::with(['company'])->withCount('assignedLeads')->latest();

        // Multi-tenant isolation for non-superadmin
        if ($currentUser && ! $currentUser->isSuperAdmin() && $currentUser->company_id) {
            $query->where('company_id', $currentUser->company_id);
        }

        // Sales Manager only sees their sales subordinates (Team Leader & Sales Agent)
        // Company Owner and Super Admin see all departments and roles
        $isSalesManager = $currentUser && $currentUser->isSalesManager();
        if ($isSalesManager) {
            $query->whereIn('role', [UserRole::TEAM_LEADER, UserRole::SALES_AGENT]);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name, email, phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $baseCountQuery = User::query();
        if ($currentUser && ! $currentUser->isSuperAdmin() && $currentUser->company_id) {
            $baseCountQuery->where('company_id', $currentUser->company_id);
        }

        if ($isSalesManager) {
            $roles = [UserRole::TEAM_LEADER, UserRole::SALES_AGENT];
            $metrics = [
                'total' => (clone $baseCountQuery)->whereIn('role', [UserRole::TEAM_LEADER, UserRole::SALES_AGENT])->count(),
                'team_leaders' => (clone $baseCountQuery)->where('role', UserRole::TEAM_LEADER)->count(),
                'sales_agents' => (clone $baseCountQuery)->where('role', UserRole::SALES_AGENT)->count(),
                'active_sales' => (clone $baseCountQuery)->whereIn('role', [UserRole::TEAM_LEADER, UserRole::SALES_AGENT])->where('is_active', true)->count(),
            ];
        } else {
            $roles = UserRole::cases();
            $metrics = [
                'total' => (clone $baseCountQuery)->count(),
                'sales' => (clone $baseCountQuery)->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])->count(),
                'managerial' => (clone $baseCountQuery)->whereIn('role', [UserRole::SALES_MANAGER, UserRole::COMPANY_OWNER, UserRole::SUPER_ADMIN])->count(),
                'finance' => (clone $baseCountQuery)->where('role', UserRole::FINANCE)->count(),
                'property_admin' => (clone $baseCountQuery)->where('role', UserRole::ADMIN_PROPERTY)->count(),
            ];
        }

        return view('settings.users.index', [
            'users' => $users,
            'roles' => $roles,
            'metrics' => $metrics,
            'isSalesManager' => $isSalesManager,
            'filters' => $request->only(['role', 'search']),
        ]);
    }

    /**
     * Toggle active status of a user with strict role hierarchy enforcement.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        // Enforce role hierarchy and permissions
        if (! $currentUser->canManageUser($user)) {
            if ($user->id === $currentUser->id) {
                return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            }

            if ($user->isCompanyOwner() || $user->isSuperAdmin()) {
                return back()->with('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menonaktifkan akun Company Owner atau Super Admin.');
            }

            return back()->with('error', 'Akses Ditolak: Anda hanya memiliki wewenang mengelola status akun anggota tim sales (Team Leader & Sales Agent).');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status akun {$user->name} ({$user->role->label()}) berhasil {$statusText}.");
    }
}
