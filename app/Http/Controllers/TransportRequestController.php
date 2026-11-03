<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\TransportRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    public function index(Request $request): View
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
            ->orderByDesc('submitted_at')
            ->paginate(3)
            ->withQueryString();

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

        return view('requests.index', [
            'requests' => $requests,
            'statuses' => self::STATUSES,
            'q' => $q,
            'status' => $status,
            'availableDrivers' => $availableDrivers,
        ]);
    }
}
