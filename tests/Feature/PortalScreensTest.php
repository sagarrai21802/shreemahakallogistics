<?php

namespace Tests\Feature;

use App\Models\TransportRequest;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalScreensTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoDataSeeder::class);
    }

    public function test_guests_are_redirected_from_portal_screens(): void
    {
        foreach (['/requests', '/drivers', '/applications', '/activity', '/help', '/account'] as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_transport_requests_can_be_filtered_and_searched(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/requests?status=confirmed')
            ->assertOk()
            ->assertSee('TRQ-2026-0149')
            ->assertDontSee('TRQ-2026-0142');

        $this->actingAs($user)->get('/requests?q=TRQ-2026-0147')
            ->assertOk()
            ->assertSee('TRQ-2026-0147')
            ->assertDontSee('TRQ-2026-0142');
    }

    public function test_drivers_can_be_filtered_by_availability(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/drivers?availability=available')
            ->assertOk()
            ->assertSee('Suresh Patel')
            ->assertDontSee('Arjun Mehta');

        $this->actingAs($user)->get('/drivers')
            ->assertOk()
            ->assertSee('DRV-1042')
            ->assertSee('MP09 AB 1234');
    }

    public function test_applications_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/applications?status=needs_information')
            ->assertOk()
            ->assertSee('Sanjay Gupta')
            ->assertDontSee('Ravi Chauhan');
    }

    public function test_activity_can_be_filtered_by_type(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/activity?type=driver_rejected')
            ->assertOk()
            ->assertSee('Driver declined an invitation')
            ->assertDontSee('New request received');
    }

    public function test_help_and_account_screens_render(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/help')
            ->assertOk()
            ->assertSee('Help &amp; Support', false)
            ->assertSee('support@shreemahakal.test');

        $this->actingAs($user)->get('/account')
            ->assertOk()
            ->assertSee('My Account')
            ->assertSee($user->username);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/account/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect('/account')->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_password_update_requires_the_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/account/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_demo_data_can_be_reset(): void
    {
        $user = User::factory()->create();

        TransportRequest::query()->create([
            'reference' => 'TEST-0001',
            'customer_name' => 'Test Customer',
            'customer_phone' => '+91 90000 00000',
            'pickup_address' => 'Test pickup',
            'pickup_city' => 'Indore',
            'destination_address' => 'Test destination',
            'destination_city' => 'Bhopal',
            'goods_category' => 'Packages',
            'goods_description' => 'Test goods',
            'quantity' => '1 box',
            'weight_kg' => 10,
            'status' => 'new',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)->post('/demo/reset')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('transport_requests', ['reference' => 'TEST-0001']);
        $this->assertDatabaseHas('transport_requests', ['reference' => 'TRQ-2026-0142']);
    }
}
