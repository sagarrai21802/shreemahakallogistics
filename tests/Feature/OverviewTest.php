<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoDataSeeder::class);
    }

    public function test_guests_cannot_view_the_overview(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_overview_shows_the_operational_summary(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Overview');
        $response->assertSee('New requests');
        $response->assertSee('TRQ-2026-0143');
        $response->assertViewHas('stats', fn (array $stats) => isset($stats['new_requests']) && $stats['available_drivers'] === 5);
    }

    public function test_overview_lists_the_application_review_queue(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Driver applications')
            ->assertSee('Ravi Chauhan');
    }
}
