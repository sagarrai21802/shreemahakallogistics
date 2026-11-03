<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanTransportRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoDataSeeder::class);
    }

    public function test_authenticated_user_can_view_kanban_board(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/requests');

        $response->assertOk();
        $response->assertSee('New Request');
        $response->assertSee('Request Sent to Drivers');
        $response->assertSee('Confirmed (In Progress)');
        $response->assertSee('Completed');
        $response->assertSee('kanban-board', false);
        $response->assertSee('kanban-card', false);
    }
}
