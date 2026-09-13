<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_custom_403_page_renders_cleanly(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();

        // Accessing settings.users without permission triggers 403
        $response = $this->actingAs($sales)->get(route('settings.users.index'));

        $response->assertStatus(403);
        $response->assertSee('Akses Wewenang Ditolak');
        $response->assertSee('403 — Forbidden');
        $response->assertSee('Kembali ke Halaman Sebelumnya');
    }

    public function test_custom_404_page_renders_cleanly(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/halaman-fiktif-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('Data atau Halaman Tidak Ditemukan');
        $response->assertSee('404 — Not Found');
    }
}
