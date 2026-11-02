<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_users_can_sign_in_with_a_username_and_password(): void
    {
        $user = User::factory()->create([
            'username' => 'dispatcher',
            'password' => 'secret-password',
        ]);

        $response = $this->post('/login', [
            'username' => 'dispatcher',
            'password' => 'secret-password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_sign_in_with_invalid_credentials(): void
    {
        User::factory()->create(['username' => 'dispatcher']);

        $response = $this->from('/login')->post('/login', [
            'username' => 'dispatcher',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    public function test_users_can_sign_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
