<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public const TYPES = [
        'request_received',
        'invitation_sent',
        'driver_accepted',
        'driver_rejected',
        'follow_up',
        'application_submitted',
        'application_approved',
    ];

    public function index(Request $request): View
    {
        $type = (string) $request->query('type', '');

        if (! in_array($type, self::TYPES, true)) {
            $type = '';
        }

        $events = ActivityEvent::query()
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        return view('activity.index', [
            'events' => $events,
            'types' => self::TYPES,
            'type' => $type,
        ]);
    }
}
