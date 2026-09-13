<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile edit form.
     */
    public function edit(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $accountStats = [
            'joined_at' => $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-',
            'role_label' => $user->role->label(),
            'company_name' => $user->company->name ?? 'PROPFlow System',
        ];

        if ($user->isSales()) {
            $accountStats['total_leads'] = $user->assignedLeads()->count();
        }

        return view('profile.edit', [
            'user' => $user,
            'accountStats' => $accountStats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request, \App\Services\ImageOptimizerService $imageOptimizer): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $avatarPath = $imageOptimizer->convertToWebp($request->file('avatar'), 'avatars');
            $user->avatar_path = $avatarPath;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->timezone = $validated['timezone'] ?? $user->timezone ?? 'Asia/Jakarta';
        $user->bank_name = $validated['bank_name'] ?? null;
        $user->bank_account_number = $validated['bank_account_number'] ?? null;
        $user->bank_account_holder = $validated['bank_account_holder'] ?? null;
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil akun Anda berhasil diperbarui.');
    }

    /**
     * Update the user's account password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Kata sandi keamanan akun Anda berhasil diperbarui.');
    }

    /**
     * Delete the user's avatar image.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Foto profil berhasil dihapus dan kembali ke inisial nama.');
    }
}
