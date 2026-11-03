<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\DriverInvitation;
use App\Models\TransportRequest;
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
        $response->assertSee('Requests');
        $response->assertSee('New Request');
        $response->assertSee('Request Sent to Driver');
        $response->assertSee('Driver Accepts');
        $response->assertSee('Active Requests');
        $response->assertSee('Completed Requests');
        $response->assertSee('Reset to New');
        $response->assertSee('Change Status');
        $response->assertSee('Blinkit Enterprise');
        $response->assertSee('Available Drivers Nearby');
        $response->assertDontSee('Manage Status');
        $response->assertDontSee('id="req-filters-btn"', false);
        $response->assertDontSee('id="req-status-btn"', false);
    }

    public function test_resetting_status_moves_the_request_back_to_new(): void
    {
        $user = User::factory()->create();
        $transportRequest = TransportRequest::query()->where('reference', 'TRQ-2026-0143')->firstOrFail();
        $transportRequest->update(['status' => 'awaiting_responses']);

        $response = $this->actingAs($user)->patchJson("/requests/{$transportRequest->id}/status", [
            'status' => 'new',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Request moved to New.');
        $this->assertSame('new', $transportRequest->fresh()->status);
    }

    public function test_resetting_to_new_deletes_driver_invitations(): void
    {
        $user = User::factory()->create();
        $transportRequest = TransportRequest::query()->where('reference', 'TRQ-2026-0143')->firstOrFail();
        $driver = Driver::query()->firstOrFail();

        DriverInvitation::query()->create([
            'transport_request_id' => $transportRequest->id,
            'driver_id' => $driver->id,
            'response_status' => 'pending',
            'sent_at' => now(),
        ]);

        $transportRequest->update(['status' => 'awaiting_responses']);

        $this->actingAs($user)->patchJson("/requests/{$transportRequest->id}/status", [
            'status' => 'new',
        ])->assertOk();

        $this->assertSame('new', $transportRequest->fresh()->status);
        $this->assertSame(0, $transportRequest->invitations()->count());
    }

    public function test_unauthenticated_status_update_returns_401(): void
    {
        $transportRequest = TransportRequest::query()->where('reference', 'TRQ-2026-0143')->firstOrFail();

        $this->patchJson("/requests/{$transportRequest->id}/status", [
            'status' => 'new',
        ])->assertUnauthorized();
    }

    public function test_master_control_can_update_status_to_any_stage(): void
    {
        $user = User::factory()->create();
        $transportRequest = TransportRequest::query()->where('reference', 'TRQ-2026-0143')->firstOrFail();

        foreach (['awaiting_responses', 'driver_accepted', 'confirmed', 'closed', 'new'] as $targetStatus) {
            $this->actingAs($user)->patchJson("/requests/{$transportRequest->id}/status", [
                'status' => $targetStatus,
            ])->assertOk();

            $this->assertSame($targetStatus, $transportRequest->fresh()->status);
        }
    }
}
