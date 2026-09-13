<?php

namespace App\Http\Controllers;

use App\Enums\IndonesianTimezone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimezoneController extends Controller
{
    /**
     * Switch user's active Indonesian timezone (WIB, WITA, or WIT).
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', Rule::enum(IndonesianTimezone::class)],
        ], [
            'timezone.required' => 'Zona waktu wajib dipilih.',
            'timezone.enum' => 'Zona waktu harus salah satu dari 3 zona waktu Indonesia (WIB, WITA, atau WIT).',
        ]);

        $selectedTz = IndonesianTimezone::from($validated['timezone']);

        if ($request->user()) {
            if ($request->user()->company) {
                // Update PT timezone so all employees/staff in this PT use this timezone
                $request->user()->company->update([
                    'timezone' => $selectedTz->value,
                ]);
            }

            $request->user()->update([
                'timezone' => $selectedTz->value,
            ]);
        }

        $request->session()->put('timezone', $selectedTz->value);

        return back()->with('success', "Zona waktu operasional berhasil dialihkan ke {$selectedTz->code()} ({$selectedTz->utcOffset()}).");
    }
}
