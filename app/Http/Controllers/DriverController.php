<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public const APPROVAL_STATUSES = ['pending', 'approved', 'needs_information', 'rejected'];

    public const AVAILABILITY_STATUSES = ['available', 'busy', 'offline', 'location_stale'];

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $approval = (string) $request->query('approval', '');
        $availability = (string) $request->query('availability', '');

        if (! in_array($approval, self::APPROVAL_STATUSES, true)) {
            $approval = '';
        }

        if (! in_array($availability, self::AVAILABILITY_STATUSES, true)) {
            $availability = '';
        }

        $drivers = Driver::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('partner_ref', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('registration_number', 'like', "%{$q}%")
                        ->orWhere('vehicle_type', 'like', "%{$q}%");
                });
            })
            ->when($approval !== '', fn ($query) => $query->where('approval_status', $approval))
            ->when($availability !== '', fn ($query) => $query->where('availability_status', $availability))
            ->orderByRaw("CASE WHEN availability_status = 'available' THEN 0 WHEN availability_status = 'busy' THEN 1 WHEN availability_status = 'offline' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('drivers.index', [
            'drivers' => $drivers,
            'approvalStatuses' => self::APPROVAL_STATUSES,
            'availabilityStatuses' => self::AVAILABILITY_STATUSES,
            'q' => $q,
            'approval' => $approval,
            'availability' => $availability,
        ]);
    }
}
