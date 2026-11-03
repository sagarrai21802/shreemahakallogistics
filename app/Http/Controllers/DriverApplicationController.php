<?php

namespace App\Http\Controllers;

use App\Models\DriverApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverApplicationController extends Controller
{
    public const STATUSES = ['pending_review', 'needs_information', 'approved', 'rejected'];

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        if (! in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $applications = DriverApplication::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByRaw("CASE WHEN status = 'pending_review' THEN 0 WHEN status = 'needs_information' THEN 1 ELSE 2 END")
            ->orderByDesc('submitted_at')
            ->paginate(10)
            ->withQueryString();

        return view('applications.index', [
            'applications' => $applications,
            'statuses' => self::STATUSES,
            'status' => $status,
        ]);
    }
}
