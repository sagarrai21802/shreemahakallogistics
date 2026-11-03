<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\Driver;
use App\Models\DriverInvitation;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransportRequestController extends Controller
{
    public const STATUSES = [
        'new',
        'ready_to_match',
        'invitations_sent',
        'awaiting_responses',
        'driver_accepted',
        'follow_up_needed',
        'confirmed',
        'closed',
        'cancelled',
        'no_driver_available',
    ];

    public function index(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        if (! in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $requests = TransportRequest::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('reference', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('pickup_city', 'like', "%{$q}%")
                        ->orWhere('destination_city', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->withCount('invitations as invited_count')
            ->withCount([
                'invitations as accepted_count' => fn ($query) => $query->where('response_status', 'accepted'),
            ])
            ->with(['invitations' => fn ($query) => $query
                ->with('driver:id,name,phone,vehicle_type,registration_number')])
            ->orderByDesc('submitted_at')
            ->get();

        $availableDrivers = Driver::query()
            ->where('approval_status', 'approved')
            ->where('availability_status', 'available')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->orderBy('id')
            ->get([
                'id',
                'partner_ref',
                'name',
                'phone',
                'vehicle_type',
                'vehicle_model',
                'registration_number',
                'capacity_tons',
                'lat',
                'lng',
            ]);

        return response()
            ->view('requests.index', [
                'requests' => $requests,
                'statuses' => self::STATUSES,
                'q' => $q,
                'status' => $status,
                'availableDrivers' => $availableDrivers,
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function sendDrivers(Request $request, TransportRequest $transportRequest): JsonResponse
    {
        $validated = $request->validate([
            'driver_ids' => ['required', 'array', 'min:1'],
            'driver_ids.*' => ['integer', 'distinct', 'exists:drivers,id'],
        ]);

        $driverIds = array_map(fn ($id) => (int) $id, $validated['driver_ids']);

        $availableDriverIds = Driver::query()
            ->whereIn('id', $driverIds)
            ->where('approval_status', 'approved')
            ->where('availability_status', 'available')
            ->pluck('id')
            ->all();

        if (count($availableDriverIds) !== count($driverIds)) {
            return response()->json([
                'message' => 'One or more selected drivers are no longer available.',
            ], 422);
        }

        DB::transaction(function () use ($transportRequest, $driverIds): void {

            foreach ($driverIds as $driverId) {
                $invitation = DriverInvitation::query()->firstOrCreate(
                    [
                        'transport_request_id' => $transportRequest->id,
                        'driver_id' => $driverId,
                    ],
                    [
                        'response_status' => 'pending',
                        'sent_at' => now(),
                    ],
                );

                if (! $invitation->wasRecentlyCreated && $invitation->response_status === 'pending') {
                    $invitation->update(['sent_at' => now()]);
                }
            }

            $transportRequest->update(['status' => 'awaiting_responses']);

            ActivityEvent::query()->create([
                'type' => 'invitation_sent',
                'title' => 'Invitations sent',
                'description' => sprintf('%d driver%s notified for %s', count($driverIds), count($driverIds) === 1 ? '' : 's', $transportRequest->reference),
                'occurred_at' => now(),
            ]);
        });

        return response()->json([
            'message' => sprintf('Request sent to %d driver%s', count($driverIds), count($driverIds) === 1 ? '' : 's'),
        ]);
    }

    public function updateStatus(Request $request, TransportRequest $transportRequest): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'new',
                'awaiting_responses',
                'driver_accepted',
                'confirmed',
                'closed',
            ])],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
        ]);

        if ($validated['status'] === 'confirmed' && ! empty($validated['driver_id'])) {
            $transportRequest->invitations()
                ->where('driver_id', $validated['driver_id'])
                ->update(['response_status' => 'accepted', 'responded_at' => now()]);
        }

        if ($validated['status'] === 'driver_accepted') {
            $transportRequest->invitations()
                ->where('response_status', 'pending')
                ->update(['response_status' => 'accepted', 'responded_at' => now()]);
        }

        if ($validated['status'] === 'new') {
            $transportRequest->invitations()->delete();
        }

        $transportRequest->update(['status' => $validated['status']]);

        ActivityEvent::query()->create([
            'type' => 'request_status_updated',
            'title' => 'Request status updated',
            'description' => sprintf('%s moved to %s', $transportRequest->reference, str($validated['status'])->replace('_', ' ')->title()),
            'occurred_at' => now(),
        ]);

        return response()->json([
            'message' => 'Request moved to '.str($validated['status'])->replace('_', ' ')->title().'.',
        ]);
    }
}
