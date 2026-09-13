<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('PROPFlow');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'owner@propflow.local')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Executive Analytics Dashboard');
    }

    public function test_authenticated_user_can_access_bookings_index(): void
    {
        $user = User::where('email', 'owner@propflow.local')->first();
        $this->assertTrue($user->isManagerial());

        $response = $this->actingAs($user)->get('/sales/bookings');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pemesanan');
    }
}
