<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sales Order System');
        $response->assertSee('LOGIN');
    }

    public function test_admin_can_access_orders(): void
    {
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/orders');
            $response->assertStatus(200);
            $response->assertSee('Sales Order Management (User + Admin)');
        }
    }

    public function test_user_can_access_orders(): void
    {
        $user = User::where('username', 'user')->first();
        if ($user) {
            $response = $this->actingAs($user)->get('/orders');
            $response->assertStatus(200);
            $response->assertSee('Sales Order Management (User + Admin)');
        }
    }
}
