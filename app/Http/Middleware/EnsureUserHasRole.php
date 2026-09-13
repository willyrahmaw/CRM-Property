<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request and verify user role permissions.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin has universal master access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        foreach ($roles as $role) {
            // Group Aliases
            if ($role === 'managerial' && $user->isManagerial()) {
                return $next($request);
            }

            if ($role === 'sales' && $user->isSales()) {
                return $next($request);
            }

            if ($role === 'finance_team' && ($user->isFinance() || $user->isManagerial())) {
                return $next($request);
            }

            if ($role === 'inventory_team' && ($user->isAdminProperty() || $user->isManagerial())) {
                return $next($request);
            }

            // Direct Role Value Match
            if ($role === $userRoleValue) {
                return $next($request);
            }
        }

        abort(Response::HTTP_FORBIDDEN, "Akses Ditolak: Peran akun Anda ({$user->role->label()}) tidak memiliki wewenang untuk mengakses menu atau tindakan ini.");
    }
}
