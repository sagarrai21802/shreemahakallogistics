<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\Driver;
use App\Models\DriverApplication;
use App\Models\TransportRequest;
use Illuminate\View\View;

class OverviewController extends Controller
{
    private const LOCATION_FRESHNESS_MINUTES = 15;

    public function index(): View
    {
        $freshSince = now()->subMinutes(self::LOCATION_FRESHNESS_MINUTES);

        $stats = [
            'new_requests' => TransportRequest::query()->where('status', 'new')->count(),
            'awaiting_responses' => TransportRequest::query()
                ->whereHas('invitations', fn ($query) => $query->where('response_status', 'pending'))
                ->whereNotIn('status', ['closed', 'cancelled', 'confirmed'])
                ->count(),
            'follow_up' => TransportRequest::query()
                ->whereIn('status', ['follow_up_needed', 'driver_accepted'])
                ->count(),
            'available_drivers' => Driver::query()
                ->where('approval_status', 'approved')
                ->where('availability_status', 'available')
                ->where('last_location_at', '>=', $freshSince)
                ->count(),
            'pending_applications' => DriverApplication::query()
                ->where('status', 'pending_review')
                ->count(),
        ];

        $oldestNewRequest = TransportRequest::query()
            ->where('status', 'new')
            ->oldest('submitted_at')
            ->first();

        $attention = TransportRequest::query()
            ->whereIn('status', [
                'new',
                'ready_to_match',
                'invitations_sent',
                'awaiting_responses',
                'driver_accepted',
                'follow_up_needed',
            ])
            ->withCount('invitations as invited_count')
            ->withCount([
                'invitations as accepted_count' => fn ($query) => $query->where('response_status', 'accepted'),
            ])
            ->orderByDesc('submitted_at')
            ->limit(8)
            ->get();

        $availability = Driver::query()
            ->where('approval_status', 'approved')
            ->selectRaw('availability_status, COUNT(*) as total')
            ->groupBy('availability_status')
            ->pluck('total', 'availability_status');

        $applications = DriverApplication::query()
            ->whereIn('status', ['pending_review', 'needs_information'])
            ->orderByRaw("CASE WHEN status = 'pending_review' THEN 0 ELSE 1 END")
            ->orderByDesc('submitted_at')
            ->limit(3)
            ->get();

        $activity = ActivityEvent::query()
            ->orderByDesc('occurred_at')
            ->limit(6)
            ->get();

        return view('overview', [
            'stats' => $stats,
            'oldestNewRequest' => $oldestNewRequest,
            'attention' => $attention,
            'availability' => $availability,
            'applications' => $applications,
            'activity' => $activity,
            'freshnessMinutes' => self::LOCATION_FRESHNESS_MINUTES,
        ]);
    }
}
