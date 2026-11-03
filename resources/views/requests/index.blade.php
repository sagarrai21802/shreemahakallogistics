@extends('layouts.app')

@section('title', 'Transport Requests')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Transport Requests</h1>
        </div>

        <div class="req-actions">
            <button type="button" id="req-master-status-btn" class="req-master-status-btn" title="Change status of selected request (Master Control)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Change Status
            </button>
            <button type="button" id="req-reset-btn" class="req-reset-btn" title="Reset the selected request to New Request">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                </svg>
                Reset to New
            </button>
        </div>
    </header>

    <dialog id="accepted-drivers-dialog" class="req-dialog">
        <form id="accepted-drivers-form">
            <div class="req-dialog__head">
                <span class="req-dialog__title">Drivers who accepted</span>
                <button type="button" class="req-dialog__close" data-accepted-close aria-label="Close">×</button>
            </div>
            <div class="req-dialog__body" id="accepted-drivers-list"></div>
            <div class="req-dialog__foot">
                <button type="button" class="btn btn--quiet" data-accepted-close>Cancel</button>
                <button class="btn btn--primary" type="submit">Confirm Selected Driver</button>
            </div>
        </form>
    </dialog>

    <!-- Floating Driver Details Popover Card -->
    <div id="driver-popover" class="driver-popover" hidden>
        <div class="driver-popover__head">
            <span class="driver-popover__ref" id="pop-driver-ref">DRV-1021</span>
            <h4 class="driver-popover__name" id="pop-driver-name">Driver Name</h4>
        </div>
        <div class="driver-popover__body">
            <div class="driver-popover__row">
                <span class="driver-popover__label">Phone:</span>
                <span class="driver-popover__val" id="pop-driver-phone">-</span>
            </div>
            <div class="driver-popover__row">
                <span class="driver-popover__label">Vehicle:</span>
                <span class="driver-popover__val" id="pop-driver-vehicle">-</span>
            </div>
            <div class="driver-popover__row">
                <span class="driver-popover__label">Registration:</span>
                <span class="driver-popover__val" id="pop-driver-reg">-</span>
            </div>
            <div class="driver-popover__row">
                <span class="driver-popover__label">Capacity:</span>
                <span class="driver-popover__val" id="pop-driver-capacity">-</span>
            </div>
            <div class="driver-popover__row">
                <span class="driver-popover__label">ETA:</span>
                <span class="driver-popover__val driver-popover__val--eta" id="pop-driver-eta">-</span>
            </div>
            <div class="driver-popover__row">
                <span class="driver-popover__label">Distance:</span>
                <span class="driver-popover__val" id="pop-driver-dist">-</span>
            </div>
        </div>
    </div>

    <dialog id="master-status-dialog" class="req-dialog">
        <form id="master-status-form">
            <div class="req-dialog__head">
                <span class="req-dialog__title">Master Control: Change Status</span>
                <button type="button" class="req-dialog__close" data-master-status-close aria-label="Close">×</button>
            </div>
            <div class="req-dialog__body">
                <p style="margin: 0 0 12px; font-size: 13px; color: var(--color-text-secondary);">
                    Select new status for <strong id="master-status-req-ref" style="color: var(--color-text-primary);">-</strong>:
                </p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label class="accepted-driver-option">
                        <input type="radio" name="master_status" value="new">
                        <span class="accepted-driver-option__details">
                            <strong>New Request</strong>
                            <span class="accepted-driver-option__meta">Tab 1 (Requests) · Stage 1</span>
                        </span>
                    </label>
                    <label class="accepted-driver-option">
                        <input type="radio" name="master_status" value="awaiting_responses">
                        <span class="accepted-driver-option__details">
                            <strong>Request Sent to Driver</strong>
                            <span class="accepted-driver-option__meta">Tab 1 (Requests) · Stage 2</span>
                        </span>
                    </label>
                    <label class="accepted-driver-option">
                        <input type="radio" name="master_status" value="driver_accepted">
                        <span class="accepted-driver-option__details">
                            <strong>Driver Accepts</strong>
                            <span class="accepted-driver-option__meta">Tab 1 (Requests) · Stage 3</span>
                        </span>
                    </label>
                    <label class="accepted-driver-option">
                        <input type="radio" name="master_status" value="confirmed">
                        <span class="accepted-driver-option__details">
                            <strong>Active (Confirmed)</strong>
                            <span class="accepted-driver-option__meta">Tab 2 (Active Requests)</span>
                        </span>
                    </label>
                    <label class="accepted-driver-option">
                        <input type="radio" name="master_status" value="closed">
                        <span class="accepted-driver-option__details">
                            <strong>Completed</strong>
                            <span class="accepted-driver-option__meta">Tab 3 (Completed Requests)</span>
                        </span>
                    </label>
                </div>
            </div>
            <div class="req-dialog__foot">
                <button type="button" class="btn btn--quiet" data-master-status-close>Cancel</button>
                <button class="btn btn--primary" type="submit">Update Status</button>
            </div>
        </form>
    </dialog>

    @php
        $columns = [
            'new_requests' => [
                'title' => 'Requests',
                'stages' => [
                    ['key' => 'new', 'label' => 'New Request', 'statuses' => ['new', 'ready_to_match']],
                    ['key' => 'awaiting_responses', 'label' => 'Request Sent to Driver', 'statuses' => ['invitations_sent', 'awaiting_responses']],
                    ['key' => 'driver_accepted', 'label' => 'Driver Accepts', 'statuses' => ['driver_accepted', 'follow_up_needed']],
                ],
            ],
            'active_requests' => [
                'title' => 'Active Requests',
                'stages' => [
                    ['key' => 'confirmed', 'label' => 'Confirm', 'statuses' => ['confirmed']],
                ],
            ],
            'completed_requests' => [
                'title' => 'Completed Requests',
                'stages' => [
                    ['key' => 'closed', 'label' => 'Completed', 'statuses' => ['closed', 'cancelled', 'no_driver_available']],
                ],
            ],
        ];

        foreach ($columns as $colKey => $colDef) {
            $columns[$colKey]['statuses'] = collect($colDef['stages'])->pluck('statuses')->flatten()->all();
        }

        $groupedRequests = [
            'new_requests' => collect(),
            'active_requests' => collect(),
            'completed_requests' => collect(),
        ];

        foreach ($requests as $req) {
            $assigned = false;
            foreach ($columns as $colKey => $colDef) {
                if (in_array($req->status, $colDef['statuses'], true)) {
                    $groupedRequests[$colKey]->push($req);
                    $assigned = true;
                    break;
                }
            }
            if (! $assigned) {
                $groupedRequests['new_requests']->push($req);
            }
        }
    @endphp

    <section class="kanban-section request-manager" aria-label="Transport requests">
        {{-- Rectangular Segmented Stepper tab switcher --}}
        <div class="req-stepper" role="tablist" aria-label="Request lifecycle">
            <div class="req-stepper__glider" id="req-stepper-glider"></div>
            @foreach ($columns as $tabKey => $tab)
                <button type="button" class="req-stepper__tab {{ $loop->first ? 'is-active' : '' }}" role="tab" data-request-tab="{{ $tabKey }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $tab['title'] }}<span class="req-stepper__badge">{{ $groupedRequests[$tabKey]->count() }}</span>
                </button>
            @endforeach
        </div>

        {{-- One unified WA-style box: left sidebar + right detail pane --}}
        <div class="req-workspace">
            {{-- LEFT: scrollable card list --}}
            <div class="req-sidebar">
                @foreach ($columns as $colKey => $col)
                    @php $items = $groupedRequests[$colKey]; @endphp
                    <div class="req-sidebar__group" data-column="{{ $colKey }}" id="kanban-col-{{ $colKey }}">
                        @forelse ($items as $request)
                            <div class="req-card request-row"
                                id="req-card-{{ $request->reference }}"
                                data-request-id="{{ $request->reference }}"
                                data-db-id="{{ $request->id }}"
                                data-status="{{ $request->status }}"
                                data-pickup="{{ $request->pickup_city }}"
                                data-pickup-address="{{ $request->pickup_address }}"
                                data-destination="{{ $request->destination_city }}"
                                data-destination-address="{{ $request->destination_address }}"
                                data-goods="{{ $request->goods_category }}"
                                data-goods-desc="{{ $request->goods_description }}"
                                data-weight="{{ number_format($request->weight_kg) }}"
                                data-quantity="{{ $request->quantity }}"
                                data-customer="{{ $request->customer_name }}"
                                data-phone="{{ $request->customer_phone }}"
                                data-email="{{ $request->customer_email }}"
                                data-vehicle="{{ $request->vehicle_type_needed }}"
                                data-fragile="{{ $request->is_fragile ? 'Yes' : 'No' }}"
                                data-loading="{{ $request->needs_loading_help ? 'Required' : 'Not required' }}"
                                data-instructions="{{ $request->special_instructions }}"
                                data-date="{{ $request->preferred_date ? \Carbon\Carbon::parse($request->preferred_date)->format('d M Y') : '' }}"
                                @php
                                    $acceptedDriversList = $request->invitations->where('response_status', 'accepted')->map(fn ($invitation) => [
                                        'id' => $invitation->driver_id,
                                        'name' => $invitation->driver?->name,
                                        'phone' => $invitation->driver?->phone,
                                        'vehicle' => $invitation->driver?->vehicle_type,
                                        'registration' => $invitation->driver?->registration_number,
                                    ])->values();

                                    if ($acceptedDriversList->isEmpty()) {
                                        $suresh = $availableDrivers->firstWhere('name', 'Suresh Patel');
                                        $acceptedDriversList = collect([[
                                            'id' => $suresh?->id ?? 1,
                                            'name' => 'Suresh Patel',
                                            'phone' => $suresh?->phone ?? '+91 98263 11001',
                                            'vehicle' => $suresh?->vehicle_type ?? 'Tata 407',
                                            'registration' => $suresh?->registration_number ?? 'MP09 AB 1234',
                                        ]]);
                                    }
                                @endphp
                                data-accepted-drivers="{{ $acceptedDriversList->toJson() }}"
                                data-sent-drivers="{{ $request->invitations->map(fn ($invitation) => ['name' => $invitation->driver?->name, 'phone' => $invitation->driver?->phone, 'vehicle' => $invitation->driver?->vehicle_type, 'registration' => $invitation->driver?->registration_number])->values()->toJson() }}"
                                data-confirmed-drivers="{{ $request->invitations->where('response_status', 'accepted')->map(fn ($invitation) => ['name' => $invitation->driver?->name, 'phone' => $invitation->driver?->phone, 'vehicle' => $invitation->driver?->vehicle_type, 'registration' => $invitation->driver?->registration_number])->values()->toJson() }}"
                                data-accepted-demo="{{ $request->reference === 'TRQ-2026-0143' ? 'true' : 'false' }}"
                                role="button"
                                tabindex="0">
                                <div class="req-card__top">
                                    <span class="req-card__name">{{ $request->customer_name }}</span>
                                    @php
                                        $wDisp = $request->weight_kg >= 1000 ? round($request->weight_kg / 1000, 1) . ' ton' : number_format($request->weight_kg) . ' kg';
                                    @endphp
                                    <span class="req-card__weight">{{ $wDisp }}</span>
                                </div>
                                <div class="req-card__route">
                                    <span class="req-card__city">{{ $request->pickup_city }}</span>
                                    <span class="req-card__arrow">→</span>
                                    <span class="req-card__city">{{ $request->destination_city }}</span>
                                </div>
                            </div>
                        @empty
                            {{-- empty group, nothing shown --}}
                        @endforelse
                    </div>
                @endforeach

                @if ($requests->isEmpty())
                    <div class="req-sidebar__empty-state">
                        @if ($q !== '' || $status !== '')
                            No requests match these filters.
                            <a class="text-link" href="{{ route('requests.index') }}">Clear</a>
                        @else
                            No requests yet.
                        @endif
                    </div>
                @endif
            </div>

            {{-- RIGHT: detail pane --}}
            <div id="request-details-panel" class="request-details-panel">
                <div class="request-details-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                    </svg>
                    <p>Select a request to view details</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Find Drivers modal -->
    <div id="findDriversModal" class="fd-overlay" hidden>
        <div class="fd-modal" role="dialog" aria-modal="true" aria-labelledby="fd-title">
            <header class="fd-modal__head">
                <div class="fd-modal__head-info">
                    <h2 id="fd-title" class="fd-modal__title">Find Drivers & Assign</h2>
                    <p class="fd-modal__sub" id="fd-sub">Select available nearby drivers</p>
                </div>
                <div class="fd-modal__head-actions">
                    <button type="button" class="fd-close" data-fd-close aria-label="Close">×</button>
                </div>
            </header>

            <div class="fd-body fd-bento-body">
                <!-- Top Bento Row: Map (left) + Drivers List (right) -->
                <div class="fd-bento-top">
                    <!-- Left: Compact Map Card -->
                    <div class="fd-bento-card fd-bento-card--map">
                        <div class="fd-bento-card__head">
                            <span class="fd-bento-card__title">Route Map</span>
                            <span id="fd-info-status" class="status-badge status-badge--new">New Request</span>
                        </div>
                        <div class="fd-map-wrap">
                            <div id="fd-map" class="fd-map" aria-label="Google Maps navigation view"></div>
                            <!-- Floating Map Legend -->
                            <div class="fd-map-legend" role="region" aria-label="Route Hierarchy Legend" id="fd-legend" data-collapsed>
                                <button type="button" class="fd-legend-toggle" id="fd-legend-toggle" aria-expanded="false" aria-controls="fd-legend-items">
                                    <span class="fd-legend-title">Route System</span>
                                    <span class="fd-legend-chevron" aria-hidden="true">▾</span>
                                </button>
                                <div class="fd-legend-items" id="fd-legend-items">
                                    <button type="button" class="fd-legend-item" data-layer="delivery">
                                        <span class="fd-legend-casing"><span class="fd-legend-line fd-legend-line--delivery"></span></span>
                                        <span class="fd-legend-label">Main delivery route</span>
                                    </button>
                                    <button type="button" class="fd-legend-item" data-layer="nearest">
                                        <span class="fd-legend-casing"><span class="fd-legend-line fd-legend-line--nearest"></span></span>
                                        <span class="fd-legend-label">Nearest available truck</span>
                                    </button>
                                    <button type="button" class="fd-legend-item" data-layer="others">
                                        <span class="fd-legend-casing"><span class="fd-legend-line fd-legend-line--other"></span></span>
                                        <span class="fd-legend-label">Other available trucks (4)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Drivers List or Confirmed Driver Details -->
                    <div class="fd-bento-card fd-bento-card--drivers fd-list-wrap">
                        <!-- Candidate Drivers List View (for New / Sent / Accepted Requests) -->
                        <div class="fd-candidate-drivers-section" id="fd-candidate-drivers-section">
                            <div class="fd-list-head">
                                <button type="button" class="fd-select-all-btn" id="fd-select-all" title="Select all" aria-label="Select all">
                                    <svg class="icon-unchecked" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3.5" y="3.5" width="17" height="17" rx="4.5" stroke="currentColor" stroke-width="2.2"/>
                                    </svg>
                                    <svg class="icon-checked" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="5" fill="currentColor"/>
                                        <path d="M7.5 12.5l3 3 6-6.5" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <span class="fd-list-head__title">Select All</span>
                            </div>
                            <ul id="fd-list" class="fd-list"></ul>
                            <footer class="fd-footer">
                                <button type="button" class="fd-send" id="fd-send" disabled>Send Request</button>
                            </footer>
                        </div>

                        <!-- Confirmed Driver Details View (for Active / Confirmed Requests) -->
                        <div class="fd-confirmed-driver-section" id="fd-confirmed-driver-section" hidden>
                            <div class="fd-list-head">
                                <span class="fd-list-head__title">Confirmed Driver</span>
                                <span class="status-badge status-badge--confirmed">Assigned</span>
                            </div>
                            <div class="fd-confirmed-driver-body">
                                <div class="fd-confirmed-card">
                                    <div class="fd-confirmed-card__head">
                                        <div class="fd-confirmed-avatar">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                        </div>
                                        <div class="fd-confirmed-name-wrap">
                                            <span class="fd-confirmed-name" id="fd-conf-driver-name">Suresh Patel</span>
                                            <span class="fd-confirmed-status-text">Driver En Route</span>
                                        </div>
                                    </div>
                                    <div class="fd-confirmed-grid">
                                        <div class="fd-confirmed-item">
                                            <span class="fd-confirmed-item__label">Phone Number</span>
                                            <span class="fd-confirmed-item__val" id="fd-conf-driver-phone">+91 98263 11001</span>
                                        </div>
                                        <div class="fd-confirmed-item">
                                            <span class="fd-confirmed-item__label">Vehicle</span>
                                            <span class="fd-confirmed-item__val" id="fd-conf-driver-vehicle">Tata 407</span>
                                        </div>
                                        <div class="fd-confirmed-item">
                                            <span class="fd-confirmed-item__label">Registration</span>
                                            <span class="fd-confirmed-item__val" id="fd-conf-driver-reg">MP09 AB 1234</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bento Section: Customer Details Cards -->
                <div class="fd-bento-bottom">
                    <div class="fd-bento-grid">
                        <div class="fd-bento-item">
                            <span class="fd-bento-item__label">Customer</span>
                            <span class="fd-bento-item__val" id="fd-info-customer">-</span>
                        </div>
                        <div class="fd-bento-item">
                            <span class="fd-bento-item__label">Phone Number</span>
                            <span class="fd-bento-item__val" id="fd-info-phone">-</span>
                        </div>
                        <div class="fd-bento-item">
                            <span class="fd-bento-item__label">Source</span>
                            <div class="fd-route-details">
                                <span class="fd-route-city" id="fd-info-pickup-city">Pickup City</span>
                                <span class="fd-route-address" id="fd-info-pickup-address">Pickup Address</span>
                            </div>
                        </div>
                        <div class="fd-bento-item">
                            <span class="fd-bento-item__label">Destination</span>
                            <div class="fd-route-details">
                                <span class="fd-route-city" id="fd-info-dst-city">Destination City</span>
                                <span class="fd-route-address" id="fd-info-dst-address">Destination Address</span>
                            </div>
                        </div>
                        <div class="fd-bento-item">
                            <span class="fd-bento-item__label">Weight</span>
                            <span class="fd-bento-item__val" id="fd-info-load">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="fd-toast" class="fd-toast" hidden>
        <span id="fd-toast-text"></span>
        <button type="button" class="fd-toast-close" data-toast-close aria-label="Close notification">×</button>
    </div>

    <style>
        /* ── Page shell: static viewport, no window scroll ─────────────────── */
        .page-main:has(.kanban-section) {
            max-width: none;
            margin: 0;
            padding: 16px 20px 16px;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .page-head {
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        /* ── Section wrapper ────────────────────────────────────────────────── */
        .kanban-section {
            width: 100%;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Rectangular Segmented Stepper Control ───────────────────────── */
        .req-stepper {
            position: relative;
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            gap: 4px;
            padding: 4px;
            margin-bottom: 12px;
            flex-shrink: 0;
            background: #f1f5f9;
            border: 1px solid var(--color-border);
            border-radius: 10px;
        }

        /* The animated sliding indicator */
        .req-stepper__glider {
            position: absolute;
            top: 4px;
            left: 4px;
            height: calc(100% - 8px);
            background: #ffffff;
            border-radius: 7px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), width 220ms cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
            z-index: 1;
        }

        .req-stepper__tab {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: var(--color-text-secondary);
            padding: 7px 16px;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: color 180ms ease;
        }

        .req-stepper__tab:hover {
            color: var(--color-text-primary);
        }

        .req-stepper__tab.is-active {
            color: var(--color-brand-primary);
        }

        .req-stepper__badge {
            display: inline-grid;
            place-items: center;
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 5px;
            background: color-mix(in srgb, var(--color-border) 70%, transparent);
            font-size: 11px;
            font-weight: 700;
            color: var(--color-text-secondary);
            transition: background 180ms ease, color 180ms ease;
        }

        .req-stepper__tab.is-active .req-stepper__badge {
            background: var(--color-brand-soft);
            color: var(--color-brand-strong);
        }

        .req-stage-tabs {
            display: none;
            align-items: center;
            align-self: flex-start;
            gap: 6px;
            margin-bottom: 10px;
            flex-shrink: 0;
        }
        .req-stage-tabs.is-visible { display: inline-flex; }
        .req-stage-tab {
            border: 1px solid var(--color-border);
            border-radius: 999px;
            background: #fff;
            color: var(--color-text-secondary);
            padding: 5px 12px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        .req-stage-tab:hover { color: var(--color-text-primary); }
        .req-stage-tab.is-active {
            border-color: var(--color-brand-primary);
            background: var(--color-brand-soft);
            color: var(--color-brand-strong);
        }

        /* ── One unified workspace box (WhatsApp layout) ─────────────────────── */
        .req-workspace {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            flex: 1;
            min-height: 0;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        /* ── LEFT sidebar: list of request cards ──────────────────────────── */
        .req-sidebar {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--color-border);
            background: #ffffff;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: var(--color-border) transparent;
            min-height: 0;
        }
        .req-sidebar::-webkit-scrollbar { width: 4px; }
        .req-sidebar::-webkit-scrollbar-thumb { background: var(--color-border); border-radius: 4px; }

        .req-sidebar__group { display: none; }
        .req-sidebar__group.is-visible { display: block; }

        .req-sidebar__empty-state {
            padding: 32px 20px;
            font-size: 13px;
            color: var(--color-text-secondary);
            text-align: center;
        }

        /* ── Request card (WA-row style) ──────────────────────────────────── */
        .req-card.is-stage-hidden { display: none; }
        .req-card {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 13px 16px;
            border-bottom: 1px solid color-mix(in srgb, var(--color-border) 60%, transparent);
            cursor: pointer;
            transition: background 120ms ease;
            outline: none;
            background: #ffffff;
            text-align: left;
            position: relative;
        }
        .req-card:hover {
            background: color-mix(in srgb, var(--color-brand-soft) 40%, transparent);
        }
        .req-card:focus-visible {
            outline: 2px solid var(--color-brand-primary);
            outline-offset: -2px;
        }
        .req-card.is-selected {
            background: var(--color-brand-soft);
            border-right: 3px solid var(--color-brand-primary);
        }
        .req-card.is-selected::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--color-brand-primary);
            border-radius: 0 2px 2px 0;
        }

        /* Card rows */
        .req-card__top {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }
        .req-card__name {
            font-size: 14px;
            font-weight: 650;
            color: var(--color-text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }
        .req-card__weight {
            font-size: 11px;
            font-weight: 600;
            color: var(--color-text-muted);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .req-card__route {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            color: var(--color-text-secondary);
            font-weight: 400;
        }
        .req-card__arrow {
            color: var(--color-brand-primary);
            font-size: 11px;
        }
        .req-card__city {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }
        .req-card__bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .req-card__ref {
            font-size: 10.5px;
            color: var(--color-text-muted);
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .req-card__goods {
            font-size: 10.5px;
            color: var(--color-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── RIGHT detail panel ─────────────────────────────────────────────── */
        .request-details-panel {
            min-width: 0;
            height: 100%;
            min-height: 0;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .request-details-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            height: 100%;
            padding: 32px;
            color: var(--color-text-secondary);
            text-align: center;
            font-size: 13px;
        }
        .request-details-empty p { margin: 0; }

        /* fd-overlay/fd-modal embedded in detail panel */
        .request-details-panel .fd-overlay {
            position: static;
            display: flex;
            flex-direction: column;
            padding: 0;
            background: none;
            backdrop-filter: none;
            height: 100%;
            min-height: 0;
            width: 100%;
        }
        .request-details-panel .fd-modal {
            width: 100%;
            height: 100%;
            min-height: 0;
            max-height: none;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            flex-direction: column;
        }
        .request-details-panel .fd-close { display: none; }

        /* ── fd-modal header (compact) ──────────────────────────────────────── */
        .fd-modal__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 16px;
            border-bottom: 1px solid var(--color-border);
            background: #ffffff;
            flex-shrink: 0;
        }
        .fd-modal__head-info {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .fd-modal__tag {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 2px 7px;
            background: var(--color-brand-soft);
            color: var(--color-brand-strong);
            border-radius: 4px;
        }
        .fd-modal__title {
            font-size: 14px;
            font-weight: 650;
            margin: 0;
            color: var(--color-text-primary);
        }
        .fd-modal__sub { display: none; }
        .fd-modal__head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .fd-toolbar-inline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .fd-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--color-text-secondary);
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .fd-close:hover { background: var(--color-surface-muted); color: var(--color-text-primary); }
        .fd-label { font-size: 11.5px; font-weight: 500; color: var(--color-text-secondary); }
        .fd-select {
            padding: 3px 6px;
            border: 1px solid var(--color-border);
            border-radius: 5px;
            font-size: 11.5px;
            background: #fff;
            color: var(--color-text-primary);
        }

        /* ── Bento Grid Layout for Detail Pane ────────────────────────────── */
        .fd-bento-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 12px;
            background: #f8faf9;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            box-sizing: border-box;
        }

        .fd-bento-top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            flex: 1;
            min-height: 0;
        }

        .fd-bento-card {
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        /* Compact Map Card */
        .fd-bento-card--map {
            position: relative;
            min-height: 240px;
        }
        .fd-bento-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 14px;
            background: #ffffff;
            border-bottom: 1px solid var(--color-border);
            flex-shrink: 0;
        }
        .fd-bento-card__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted);
        }

        .fd-bento-card--map .fd-map-wrap {
            flex: 1;
            min-height: 0;
            position: relative;
        }

        /* Drivers List Card */
        .fd-bento-card--drivers {
            min-height: 0;
            border: 1px solid var(--color-border);
        }

        /* Confirmed Driver Section (for Active Requests) */
        .fd-candidate-drivers-section {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }
        .fd-candidate-drivers-section[hidden] { display: none !important; }

        .fd-confirmed-driver-section {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            background: #ffffff;
        }
        .fd-confirmed-driver-section[hidden] { display: none !important; }

        .fd-confirmed-driver-body {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            overflow-y: auto;
        }
        .fd-confirmed-card {
            background: #f8faf9;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .fd-confirmed-card__head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--color-border);
        }
        .fd-confirmed-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-brand-soft);
            color: var(--color-brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fd-confirmed-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .fd-confirmed-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text-primary);
        }
        .fd-confirmed-status-text {
            font-size: 11px;
            font-weight: 650;
            color: #15803d;
        }
        .fd-confirmed-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .fd-confirmed-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .fd-confirmed-item__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-text-muted);
        }
        .fd-confirmed-item__val {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        /* Status Badge Styling */
        .status-badge {
            display: inline-flex;
            align-items: center;
            height: 22px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 650;
            white-space: nowrap;
            transition: all 140ms ease;
        }
        .status-badge--new {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .status-badge--sent {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .status-badge--accepted {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .status-badge--confirmed {
            background: #d1fae5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .status-badge--closed {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Bottom Bento Grid */
        .fd-bento-bottom {
            flex-shrink: 0;
        }
        .fd-bento-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }
        .fd-bento-item {
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            min-width: 0;
        }
        .fd-bento-item__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted);
        }
        .fd-bento-item__val {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--color-text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Floating Driver Details Popover Card ────────────────────────────── */
        .driver-popover {
            position: fixed;
            z-index: 2000;
            width: 250px;
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.16), 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: opacity 140ms ease, transform 140ms ease;
            opacity: 0;
            transform: scale(0.96);
            pointer-events: auto;
        }
        .driver-popover.is-visible {
            opacity: 1;
            transform: scale(1);
        }
        .driver-popover[hidden] {
            display: none !important;
        }
        .driver-popover__head {
            display: flex;
            flex-direction: column;
            gap: 2px;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 8px;
        }
        .driver-popover__ref {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-brand-primary);
        }
        .driver-popover__name {
            font-size: 13.5px;
            font-weight: 700;
            margin: 0;
            color: var(--color-text-primary);
        }
        .driver-popover__body {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .driver-popover__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
        }
        .driver-popover__label {
            color: var(--color-text-muted);
            font-weight: 500;
        }
        .driver-popover__val {
            color: var(--color-text-primary);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .driver-popover__val--eta {
            color: #1a73e8;
        }

        .fd-item__info-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1px solid var(--color-border);
            background: #ffffff;
            color: var(--color-text-secondary);
            cursor: pointer;
            transition: all 140ms ease;
            flex-shrink: 0;
            padding: 0;
            margin-left: 4px;
        }
        .fd-item__info-btn:hover {
            background: var(--color-brand-soft);
            color: var(--color-brand-primary);
            border-color: var(--color-brand-primary);
        }

        .fd-map-pane {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--color-border);
            min-height: 0;
            height: 100%;
            position: relative;
            background: #e8eaed;
            overflow: hidden;
        }
        .fd-map-wrap {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .fd-map {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }
        .fd-list-wrap {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            background: #ffffff;
            overflow: hidden;
        }
        .fd-list-head {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--color-text-primary);
            border-bottom: 1px solid var(--color-border);
            background: #fafbfc;
            flex-shrink: 0;
        }
        .fd-list-head__title { font-size: 11.5px; font-weight: 600; color: var(--color-text-primary); }
        .fd-select-all-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; flex-shrink: 0;
            background: none; border: none; border-radius: 6px;
            color: var(--color-text-secondary); cursor: pointer;
            transition: all 140ms ease;
            padding: 0;
        }
        .fd-select-all-btn:hover { background: var(--color-surface-muted); color: var(--color-brand-primary); }
        .fd-select-all-btn.is-all-selected { color: var(--color-brand-primary); }
        .fd-select-all-btn .icon-checked { display: none; }
        .fd-select-all-btn .icon-unchecked { display: block; }
        .fd-select-all-btn.is-all-selected .icon-checked { display: block; }
        .fd-select-all-btn.is-all-selected .icon-unchecked { display: none; }
        .fd-list {
            list-style: none; margin: 0; padding: 0;
            overflow-y: auto; flex: 1;
            scrollbar-width: thin;
            scrollbar-color: var(--color-border) transparent;
        }
        .fd-list::-webkit-scrollbar { width: 4px; }
        .fd-list::-webkit-scrollbar-thumb { background: var(--color-border); border-radius: 4px; }
        .fd-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-bottom: 1px solid var(--color-border);
            transition: background 150ms ease; cursor: pointer;
        }
        .fd-item:hover { background: #f8faf9; }
        .fd-item--selected { background: #f1f7f4; }
        .fd-item--selected:hover { background: #ecf3ef; }
        .fd-item--nearest { background: #fdfaf2; border-left: 3px solid #202124; }
        .fd-item--nearest:hover { background: #fbf6e8; }
        .fd-item--active { background: var(--color-brand-soft) !important; }
        .fd-item input { width: 15px; height: 15px; accent-color: var(--color-brand-primary); cursor: pointer; flex-shrink: 0; }
        .fd-item__badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 50%;
            font-size: 10px; font-weight: 700; flex-shrink: 0;
        }
        .fd-item__badge--nearest { background: #202124; color: #fff; }
        .fd-item__badge--other { background: #e8eaed; color: #5f6368; }
        .fd-item__info { flex: 1; min-width: 0; }
        .fd-item__top { display: flex; align-items: center; gap: 5px; }
        .fd-item__name { font-size: 12.5px; font-weight: 600; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fd-item__tag { font-size: 9px; font-weight: 700; padding: 1px 5px; border-radius: 3px; letter-spacing: 0.3px; text-transform: uppercase; }
        .fd-item__tag--nearest { background: #202124; color: #fff; }
        .fd-item__meta { font-size: 11px; color: var(--color-text-secondary); margin-top: 1px; }
        .fd-item__metrics { text-align: right; flex-shrink: 0; }
        .fd-item__eta { font-size: 12px; font-weight: 700; color: #1a73e8; }
        .fd-item__dist { font-size: 10.5px; color: var(--color-text-secondary); }
        .fd-item__dist--blue { font-size: 12.5px; font-weight: 700; color: #1a73e8; }
        .fd-footer {
            display: flex; justify-content: space-between; align-items: center;
            padding: 9px 16px; border-top: 1px solid var(--color-border);
            background: #fafbfc; flex-shrink: 0;
        }
        .fd-selected { font-size: 12px; font-weight: 500; color: var(--color-text-secondary); }
        .fd-send {
            background: var(--color-brand-primary); color: #fff; border: none;
            padding: 7px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: background 150ms ease;
        }
        .fd-send:hover:not(:disabled) { background: var(--color-brand-strong); }
        .fd-send:disabled { opacity: .45; cursor: not-allowed; }

        /* Map helpers */
        .fd-map .gm-style-cc,
        .fd-map .gmnoprint a[href*='google'] { display: none !important; }
        .fd-map .gm-style .gm-soc, .fd-map .gm-ba-goto { display: none !important; }
        .fd-city-label {
            position: absolute;
            transform: translate(-50%, -190%);
            pointer-events: none;
            white-space: nowrap;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #111b18;
            text-shadow: 0 0 3px #fff, 0 0 6px #fff, 0 0 9px #fff;
        }

        /* Map legend */
        .fd-map-legend {
            position: absolute; bottom: 16px; left: 14px;
            background: rgba(255,255,255,0.96); backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(0,0,0,0.12); border-radius: 8px;
            padding: 8px 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            font-size: 11.5px; z-index: 5; pointer-events: auto;
            display: flex; flex-direction: column; gap: 5px; min-width: 200px;
        }
        .fd-legend-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #5f6368; }
        .fd-legend-toggle {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            width: 100%; margin: 0; padding: 0; background: none; border: none; cursor: pointer; text-align: left;
        }
        .fd-legend-chevron { font-size: 10px; color: #5f6368; transition: transform 160ms ease; }
        .fd-map-legend[data-collapsed] .fd-legend-chevron { transform: rotate(-90deg); }
        .fd-map-legend[data-collapsed] .fd-legend-items { display: none; }
        .fd-map-legend[data-collapsed] {
            justify-content: center; gap: 0; padding: 5px 10px;
            min-width: 0; border-radius: 999px; cursor: pointer;
        }
        .fd-map-legend[data-collapsed] .fd-legend-title { color: #202124; }
        .fd-legend-items { display: flex; flex-direction: column; gap: 5px; }
        .fd-legend-item {
            display: flex; align-items: center; gap: 8px; width: 100%;
            margin: 0; padding: 0; background: none; border: none;
            cursor: pointer; text-align: left; color: #202124; font-size: 11.5px; font-weight: 500; font-family: inherit;
        }
        .fd-legend-item.is-off { opacity: 0.4; }
        .fd-legend-casing {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 9px; background: #fff; border-radius: 4px;
            border: 1px solid #dcdcdc; box-shadow: 0 1px 2px rgba(0,0,0,0.06); flex-shrink: 0; padding: 1px;
        }
        .fd-legend-line { display: block; width: 100%; border-radius: 3px; }
        .fd-legend-line--delivery { background: #1A73E8; height: 5px; }
        .fd-legend-line--nearest { background: #202124; height: 4px; }
        .fd-legend-line--other { background: #929AA8; height: 3px; opacity: 0.85; }

        /* Toast */
        .fd-toast {
            position: fixed; top: 20px; right: 20px; background: #fff;
            border: 1px solid var(--color-border); border-left: 4px solid var(--color-brand-primary);
            border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 12px 36px rgba(17,27,24,.14); z-index: 1100; max-width: 380px;
        }
        .fd-toast[hidden] { display: none; }
        .fd-toast-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--color-text-secondary); }

        /* Info window */
        .fd-info-window { font-family: var(--font-sans); padding: 4px 2px; max-width: 230px; }
        .fd-info-title { font-size: 13px; font-weight: 700; color: #202124; margin: 0 0 4px; display: flex; align-items: center; gap: 6px; }
        .fd-info-tag { font-size: 9px; font-weight: 700; padding: 1px 4px; border-radius: 3px; background: #202124; color: #fff; text-transform: uppercase; }
        .fd-info-row { font-size: 11.5px; color: #5f6368; margin: 2px 0; }
        .fd-info-metrics { display: flex; gap: 12px; margin-top: 6px; padding-top: 6px; border-top: 1px solid #e8eaed; }
        .fd-info-metric-val { font-size: 12.5px; font-weight: 700; color: #1a73e8; }
        .fd-info-metric-lbl { font-size: 10px; color: #70757a; }

        /* ── Page header actions ─────────────────────────────────────────────── */
        .req-actions { display: flex; align-items: center; gap: 8px; flex: none; position: relative; }
        .req-master-status-btn {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid var(--color-border); border-radius: 999px;
            background: #ffffff; color: var(--color-brand-primary);
            padding: 7px 13px; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;
            transition: all 140ms ease;
        }
        .req-master-status-btn:hover {
            background: var(--color-brand-soft);
            color: var(--color-brand-strong);
            border-color: var(--color-brand-primary);
        }
        .req-master-status-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .req-reset-btn {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid var(--color-border); border-radius: 999px;
            background: #ffffff; color: var(--color-text-secondary);
            padding: 7px 13px; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;
            transition: all 140ms ease;
        }
        .req-reset-btn:hover {
            background: var(--color-surface-muted);
            color: var(--color-text-primary);
            border-color: #cbd5e1;
        }
        .req-reset-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .accepted-driver-option {
            display: flex; align-items: center; gap: 10px; padding: 11px;
            border: 1px solid var(--color-border); border-radius: 8px; cursor: pointer;
        }
        .accepted-driver-option__details { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .accepted-driver-option__phone { color: var(--color-brand-primary); font-weight: 700; }
        .accepted-driver-option__meta { color: var(--color-text-secondary); font-size: 12px; }
        .req-icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; flex: none;
            border: 1px solid var(--color-border); border-radius: 999px;
            background: var(--color-surface); color: var(--color-text-secondary);
            cursor: pointer;
        }
        .req-icon-btn:hover { background: var(--color-surface-muted); color: var(--color-text-primary); }
        .req-icon-btn.is-active { background: color-mix(in srgb, var(--color-brand-primary) 12%, white); border-color: var(--color-brand-primary); color: var(--color-brand-primary); }

        /* Expanding search bar */
        .req-search { position: relative; }
        .req-search__field { display: flex; align-items: center; }
        .req-search__input {
            width: 0; opacity: 0; padding: 0; border: none; outline: none;
            background: transparent; font: inherit; font-size: 13px; transition: width 200ms ease, opacity 200ms ease;
        }
        .req-search.is-open .req-search__input {
            width: 200px; opacity: 1;
            border: 1px solid var(--color-border); border-radius: 999px;
            padding: 6px 36px 6px 12px; background: var(--color-surface);
        }
        .req-search__btn {
            position: absolute; right: 4px; background: none; border: none;
            padding: 4px; cursor: pointer; color: var(--color-text-secondary); border-radius: 999px;
            display: flex; align-items: center; justify-content: center;
        }
        .req-search:not(.is-open) .req-search__btn { position: static; width: 34px; height: 34px; border: 1px solid var(--color-border); background: var(--color-surface); border-radius: 999px; }
        .req-search__btn:hover { color: var(--color-text-primary); background: var(--color-surface-muted); }
        .req-search__btn.is-active { color: var(--color-brand-primary); }
        .req-icon-btn:focus-visible, .req-search__btn:focus-visible { outline: 3px solid color-mix(in srgb, var(--color-brand-primary) 26%, transparent); }

        /* ── Filters / dialog styles ─────────────────────────────────────────── */
        .req-dialog {
            position: fixed; top: 80px; right: 24px; left: auto; bottom: auto;
            margin: 0; border: none; border-radius: 14px; padding: 0;
            box-shadow: 0 20px 48px rgba(17, 27, 24, 0.2);
            width: min(320px, calc(100vw - 40px));
        }
        .req-dialog::backdrop { background: rgba(17, 27, 24, 0.25); }
        .req-dialog form { display: flex; flex-direction: column; }
        .req-dialog__head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--color-border); }
        .req-dialog__title { font-size: 14px; font-weight: 650; color: var(--color-text-primary); }
        .req-dialog__close { background: none; border: none; font-size: 22px; line-height: 1; color: var(--color-text-secondary); cursor: pointer; width: 28px; height: 28px; border-radius: 6px; }
        .req-dialog__close:hover { background: var(--color-surface-muted); color: var(--color-text-primary); }
        .req-dialog__body { padding: 16px 18px; display: flex; flex-direction: column; gap: 6px; }
        .req-dialog__label { font-size: 12px; color: var(--color-text-secondary); }
        .req-dialog__select {
            height: 34px; padding: 0 28px 0 10px; width: 100%;
            border: 1px solid var(--color-border); border-radius: var(--radius-sm);
            background-color: var(--color-surface); color: var(--color-text-primary); font-size: 13px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2366716c' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
        }
        .req-dialog__foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--color-border); }
        .req-dialog__foot .btn { height: 32px; padding: 0 14px; font-size: 12.5px; }

        /* Modal overlay (standalone - not inside request-details-panel) */
        .fd-overlay { position: fixed; inset: 0; background: rgba(17,27,24,.55); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .fd-overlay[hidden] { display: none; }
        .fd-modal {
            background: #fff; border-radius: 16px;
            width: min(1280px, 98vw); height: 90vh; max-height: 900px;
            display: flex; flex-direction: column;
            box-shadow: 0 20px 48px rgba(17,27,24,.18); overflow: hidden;
        }

        /* ── Accepted driver dialog option ──────────────────────────────────── */
        .accepted-driver-option input[type="radio"] { accent-color: var(--color-brand-primary); }

        /* ── Status badge (global) ──────────────────────────────────────────── */
        .status-badge { display: inline-flex; align-items: center; height: 20px; padding: 0 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600; white-space: nowrap; }

        /* ── Responsive ─────────────────────────────────────────────────────── */
        @media (max-width: 900px) {
            .page-main:has(.kanban-section) { height: auto; max-height: none; overflow: visible; }
            .req-workspace { grid-template-columns: 1fr; height: auto; overflow: visible; border-radius: 8px; }
            .req-sidebar { max-height: 380px; border-right: none; border-bottom: 1px solid var(--color-border); }
            .request-details-panel { min-height: 500px; height: auto; }
        }
    </style>


    <script>
    const GM_KEY = @json(env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY'));
    const DB_AVAILABLE_DRIVERS = @json($availableDrivers ?? []);

    window.initFindDriversMap = function () {
        if (typeof window.__fdPending === 'function') {
            const pending = window.__fdPending;
            window.__fdPending = null;
            pending();
        }
    };

    (function () {
        const modal = document.getElementById('findDriversModal');
        const detailsPanel = document.getElementById('request-details-panel');
        const detailEmpty = detailsPanel ? detailsPanel.querySelector('.request-details-empty') : null;
        const mapEl = document.getElementById('fd-map');
        const list = document.getElementById('fd-list');
        const selectedEl = document.getElementById('fd-selected');
        const sendBtn = document.getElementById('fd-send');
        const sub = document.getElementById('fd-sub');
        const toast = document.getElementById('fd-toast');
        const toastText = document.getElementById('fd-toast-text');

        // Request details panel elements
        const fdTag = document.getElementById('fd-tag');
        const fdInfoStatus = document.getElementById('fd-info-status');
        const fdInfoPickupCity = document.getElementById('fd-info-pickup-city');
        const fdInfoPickupAddress = document.getElementById('fd-info-pickup-address');
        const fdInfoDstCity = document.getElementById('fd-info-dst-city');
        const fdInfoDstAddress = document.getElementById('fd-info-dst-address');
        const fdInfoCustomer = document.getElementById('fd-info-customer');
        const fdInfoPhone = document.getElementById('fd-info-phone');
        const fdInfoEmail = document.getElementById('fd-info-email');
        const fdInfoGoods = document.getElementById('fd-info-goods');
        const fdInfoLoad = document.getElementById('fd-info-load');
        const fdInfoVehicle = document.getElementById('fd-info-vehicle');
        const fdInfoDate = document.getElementById('fd-info-date');
        const fdInfoFragile = document.getElementById('fd-info-fragile');
        const fdInfoLoading = document.getElementById('fd-info-loading');
        const fdInfoDesc = document.getElementById('fd-info-desc');
        const fdInfoDescWrap = document.getElementById('fd-info-desc-wrap');
        const fdInfoInstructions = document.getElementById('fd-info-instructions');
        const fdInfoInstructionsWrap = document.getElementById('fd-info-instructions-wrap');

        let currentActiveRequest = null;

        function updateStepperGlider() {
            const stepper = document.querySelector('.req-stepper');
            const glider = document.getElementById('req-stepper-glider');
            const activeTab = stepper ? stepper.querySelector('.req-stepper__tab.is-active') : null;
            if (stepper && glider && activeTab) {
                const stepperRect = stepper.getBoundingClientRect();
                const tabRect = activeTab.getBoundingClientRect();
                const left = tabRect.left - stepperRect.left;
                const width = tabRect.width;
                glider.style.transform = `translateX(${left - 4}px)`;
                glider.style.width = `${width}px`;
            }
        }

        function applyStageFilter(tabKey) {
            document.querySelectorAll(`.req-sidebar__group[data-column="${tabKey}"] .request-row`).forEach(card => {
                card.classList.remove('is-stage-hidden');
            });
        }

        function showRequestTab(tabKey) {
            document.querySelectorAll('.req-stepper__tab, .req-pill-tab').forEach(tab => {
                const active = tab.dataset.requestTab === tabKey;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            document.querySelectorAll('.req-sidebar .req-sidebar__group').forEach(column => {
                column.classList.toggle('is-visible', column.dataset.column === tabKey);
            });

            applyStageFilter(tabKey);
            updateStepperGlider();

            document.querySelectorAll('.request-row.is-selected').forEach(card => card.classList.remove('is-selected'));

            const firstCard = document.querySelector(`.req-sidebar__group[data-column="${tabKey}"] .request-row:not(.is-stage-hidden)`);
            if (firstCard) {
                firstCard.click();
            } else {
                if (modal) modal.hidden = true;
                if (detailEmpty) detailEmpty.hidden = false;
                currentActiveRequest = null;
            }
        }

        document.querySelectorAll('.req-stepper__tab, .req-pill-tab').forEach(tab => {
            tab.addEventListener('click', () => showRequestTab(tab.dataset.requestTab));
        });
        showRequestTab('new_requests');
        window.addEventListener('resize', updateStepperGlider);
        setTimeout(updateStepperGlider, 50);

        // Location coordinates for known cities and urban pickup/delivery locations
        const locationCoords = {
            'Indore': { lat: 22.7196, lng: 75.8577 },
            'Scheme 54, Vijay Nagar, Indore': { lat: 22.7533, lng: 75.8937 },
            'Scheme 54, Vijay Nagar': { lat: 22.7533, lng: 75.8937 },
            'Transport Nagar, Indore': { lat: 22.6890, lng: 75.8390 },
            'Transport Nagar': { lat: 22.6890, lng: 75.8390 },
            'Rau, Indore': { lat: 22.6288, lng: 75.8074 },
            'Rau': { lat: 22.6288, lng: 75.8074 },
            'Sapna Sangeeta, Indore': { lat: 22.7050, lng: 75.8670 },
            'Sapna Sangeeta': { lat: 22.7050, lng: 75.8670 },
            'Palasia, Indore': { lat: 22.7244, lng: 75.8839 },
            'Palasia': { lat: 22.7244, lng: 75.8839 },
            'Dewas': { lat: 22.9676, lng: 76.0534 },
            'Industrial Area, Dewas': { lat: 22.9620, lng: 76.0450 },
            'Industrial Area': { lat: 22.9620, lng: 76.0450 },
            'Ujjain': { lat: 23.1765, lng: 75.7885 },
            'Freeganj, Ujjain': { lat: 23.1725, lng: 75.7925 },
            'Freeganj': { lat: 23.1725, lng: 75.7925 },
            'Dewas Gate, Ujjain': { lat: 23.1810, lng: 75.7760 },
            'Dewas Gate': { lat: 23.1810, lng: 75.7760 },
            'Bhopal': { lat: 23.2599, lng: 77.4126 },
            'Kolar Road, Bhopal': { lat: 23.1783, lng: 77.4244 },
            'Kolar Road': { lat: 23.1783, lng: 77.4244 },
            'MP Nagar, Bhopal': { lat: 23.2332, lng: 77.4343 },
            'MP Nagar': { lat: 23.2332, lng: 77.4343 },
            'Govindpura, Bhopal': { lat: 23.2570, lng: 77.4640 },
            'Govindpura': { lat: 23.2570, lng: 77.4640 },
            'Nagpur': { lat: 21.1458, lng: 79.0882 },
            'Sitabuldi Main Road, Nagpur': { lat: 21.1466, lng: 79.0888 },
            'Sitabuldi Main Road': { lat: 21.1466, lng: 79.0888 },
            'Kamptee Road, Nagpur': { lat: 21.1850, lng: 79.1120 },
            'Kamptee Road': { lat: 21.1850, lng: 79.1120 },
            'Pune': { lat: 18.5204, lng: 73.8567 },
            'Hadapsar, Pune': { lat: 18.5089, lng: 73.9259 },
            'Hadapsar': { lat: 18.5089, lng: 73.9259 },
            'Mumbai': { lat: 19.0760, lng: 72.8777 },
            'Bhiwandi, Mumbai': { lat: 19.2967, lng: 73.0631 },
            'Bhiwandi': { lat: 19.2967, lng: 73.0631 },
            'Surat': { lat: 21.1702, lng: 72.8311 },
            'Adajan, Surat': { lat: 21.1926, lng: 72.7997 },
            'Adajan': { lat: 21.1926, lng: 72.7997 },
            'Hyderabad': { lat: 17.3850, lng: 78.4867 },
            'Kukatpally, Hyderabad': { lat: 17.4849, lng: 78.4138 },
            'Kukatpally': { lat: 17.4849, lng: 78.4138 },
            'Jaipur': { lat: 26.9124, lng: 75.7873 },
            'Vishwakarma Industrial Area, Jaipur': { lat: 26.9855, lng: 75.7725 },
            'Vishwakarma Industrial Area': { lat: 26.9855, lng: 75.7725 },
            'Ahmedabad': { lat: 23.0225, lng: 72.5714 },
            'Prahlad Nagar, Ahmedabad': { lat: 23.0120, lng: 72.5108 },
            'Prahlad Nagar': { lat: 23.0120, lng: 72.5108 },
            'Delhi': { lat: 28.7041, lng: 77.1025 },
            'Bangalore': { lat: 12.9716, lng: 77.5946 },
        };

        // Section 4: Map Styling & Label Visibility - Show only what is relevant
        const MAP_STYLES = [
            { featureType: 'poi', elementType: 'all', stylers: [{ visibility: 'off' }] },
            { featureType: 'transit', elementType: 'all', stylers: [{ visibility: 'off' }] },
            { featureType: 'administrative.neighborhood', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            { featureType: 'administrative.locality', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            { featureType: 'road.local', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            { featureType: 'road.highway', elementType: 'geometry', stylers: [{ visibility: 'on' }] },
        ];

        let SRC = { lat: 22.7533, lng: 75.8937 };
        let DST = { lat: 22.6890, lng: 75.8390 };
        let SRC_NAME = 'Source';
        let DST_NAME = 'Destination';
        let map = null;
        let mapOverlays = [];
        let truckMarkersMap = new Map();
        let driverClusterer = null;
        let activeInfoWindow = null;
        let selected = new Set();
        let currentDriversList = [];
        let layerRoutes = { delivery: [], nearest: [], others: [] };
        let layersVisible = { delivery: true, nearest: true, others: true };

        function haversineKm(a, b) {
            const R = 6371;
            const toRad = x => x * Math.PI / 180;
            const dLat = toRad(b.lat - a.lat);
            const dLng = toRad(b.lng - a.lng);
            const x = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(a.lat)) * Math.cos(toRad(b.lat)) * Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
        }

        // Section 6: Marker Design - Clean custom SVG markers
        // Small, quiet source marker so routes and drivers remain easy to read.
        function createSourceMarkerIcon() {
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="8" fill="#0F9D58" stroke="#FFFFFF" stroke-width="3"/>
                <circle cx="12" cy="12" r="3" fill="#FFFFFF"/>
            </svg>`;
            return {
                url: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(24, 24),
                anchor: new google.maps.Point(12, 12),
            };
        }

        // Small destination marker; the route itself communicates the destination.
        function createDestinationMarkerIcon() {
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M12 2.5a7 7 0 0 0-7 7c0 5.25 7 12 7 12s7-6.75 7-12a7 7 0 0 0-7-7Z" fill="#D93025" stroke="#FFFFFF" stroke-width="2"/>
                <circle cx="12" cy="9.5" r="2.5" fill="#FFFFFF"/>
            </svg>`;
            return {
                url: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(24, 24),
                anchor: new google.maps.Point(12, 22),
            };
        }

        // Nearest truck marker: Black truck icon, highlighted, small NEAREST label
        function createNearestTruckMarkerIcon() {
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="60" height="54" viewBox="0 0 60 54">
                <defs>
                    <filter id="sdw-nrst" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="2.2" flood-color="#000" flood-opacity="0.35"/>
                    </filter>
                </defs>
                <g filter="url(#sdw-nrst)">
                    <!-- Black circular badge -->
                    <circle cx="30" cy="19" r="17" fill="#202124" stroke="#FFFFFF" stroke-width="2.5"/>
                    <!-- White truck icon silhouette -->
                    <g transform="translate(19.5 9.5) scale(0.95)">
                        <path fill="#FFFFFF" d="M17 7h-2.5V3.5H2c-.8 0-1.5.7-1.5 1.5v9h1.5c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5h5c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5h1.5v-4l-2.5-3.5zm-12.5 9c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm11 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm.5-8.5l1.6 2h-3.6V7.5h2z"/>
                    </g>
                    <!-- Small NEAREST label pill -->
                    <rect x="6" y="37" width="48" height="14" rx="7" fill="#202124" stroke="#FFFFFF" stroke-width="1.5"/>
                    <text x="30" y="47.5" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="7.5" font-weight="800" fill="#FFFFFF" text-anchor="middle" letter-spacing="0.5">NEAREST</text>
                </g>
            </svg>`;
            return {
                url: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(60, 54),
                anchor: new google.maps.Point(30, 19),
            };
        }

        // Other truck markers: Gray truck icon, smaller, numbered identifier
        function createOtherTruckMarkerIcon(num) {
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                <defs>
                    <filter id="sdw-oth" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#000" flood-opacity="0.25"/>
                    </filter>
                </defs>
                <g filter="url(#sdw-oth)">
                    <!-- Gray circular badge -->
                    <circle cx="18" cy="18" r="14" fill="#5F6368" stroke="#FFFFFF" stroke-width="2"/>
                    <!-- White truck icon silhouette -->
                    <g transform="translate(10 10) scale(0.72)">
                        <path fill="#FFFFFF" d="M17 7h-2.5V3.5H2c-.8 0-1.5.7-1.5 1.5v9h1.5c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5h5c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5h1.5v-4l-2.5-3.5zm-12.5 9c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm11 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm.5-8.5l1.6 2h-3.6V7.5h2z"/>
                    </g>
                    <!-- Small numbered badge on top-right -->
                    <circle cx="28" cy="8" r="6" fill="#929AA8" stroke="#FFFFFF" stroke-width="1.2"/>
                    <text x="28" y="10.8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="7.5" font-weight="700" fill="#FFFFFF" text-anchor="middle">${num}</text>
                </g>
            </svg>`;
            return {
                url: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(36, 36),
                anchor: new google.maps.Point(18, 18),
            };
        }

        // Section 2: Extract complete turn-by-turn road geometry without aggressive simplification
        function extractDetailedPath(directionsRoute) {
            const points = [];
            if (directionsRoute && directionsRoute.legs) {
                directionsRoute.legs.forEach(leg => {
                    if (leg.steps) {
                        leg.steps.forEach(step => {
                            if (step.path && step.path.length > 0) {
                                step.path.forEach(pt => points.push(pt));
                            } else if (step.lat_lngs && step.lat_lngs.length > 0) {
                                step.lat_lngs.forEach(pt => points.push(pt));
                            }
                        });
                    }
                });
            }
            if (points.length > 0) {
                return points;
            }
            return (directionsRoute && directionsRoute.overview_path) ? directionsRoute.overview_path : [];
        }

        // Section 1 & 3: Draw solid routes with white casing underneath and strict z-indexing
        function drawCasedRoute(targetMap, path, style) {
            // White casing polyline underneath
            const casing = new google.maps.Polyline({
                path: path,
                geodesic: false,
                strokeColor: '#FFFFFF',
                strokeOpacity: style.casingOpacity !== undefined ? style.casingOpacity : 1,
                strokeWeight: style.casingWidth,
                zIndex: style.zIndexCasing,
                map: targetMap
            });
            // Primary colored route polyline
            const line = new google.maps.Polyline({
                path: path,
                geodesic: false,
                strokeColor: style.color,
                strokeOpacity: style.opacity !== undefined ? style.opacity : 1,
                strokeWeight: style.width,
                zIndex: style.zIndexLine,
                map: targetMap
            });
            mapOverlays.push(casing, line);
            return { casing, line };
        }

        function clearMapOverlays() {
            if (driverClusterer) {
                driverClusterer.clearMarkers();
                driverClusterer = null;
            }
            mapOverlays.forEach(item => {
                if (item && typeof item.setMap === 'function') {
                    item.setMap(null);
                }
            });
            mapOverlays = [];
            truckMarkersMap.clear();
            if (activeInfoWindow) {
                activeInfoWindow.close();
                activeInfoWindow = null;
            }
        }

        // Big bold city name label rendered over the map at a position
        function addCityNameLabel(position, text) {
            if (!window.CityLabelOverlay) {
                window.CityLabelOverlay = function (position, text) {
                    this.position = position;
                    this.text = text;
                    this.div_ = null;
                };
                window.CityLabelOverlay.prototype = new google.maps.OverlayView();
                window.CityLabelOverlay.prototype.onAdd = function () {
                    this.div_ = document.createElement('div');
                    this.div_.className = 'fd-city-label';
                    this.div_.textContent = this.text;
                    this.getPanes().floatPane.appendChild(this.div_);
                };
                window.CityLabelOverlay.prototype.draw = function () {
                    const pos = this.getProjection().fromLatLngToDivPixel(this.position);
                    if (this.div_) {
                        this.div_.style.left = pos.x + 'px';
                        this.div_.style.top = pos.y + 'px';
                    }
                };
                window.CityLabelOverlay.prototype.onRemove = function () {
                    if (this.div_) {
                        this.div_.parentNode.removeChild(this.div_);
                        this.div_ = null;
                    }
                };
            }
            const overlay = new window.CityLabelOverlay(position, text);
            overlay.setMap(map);
            return overlay;
        }

        // Prepare the 5 candidate trucks for the active source
        function getCandidateDrivers(sourcePos) {
            const candidates = [];
            const count = 5;

            // Only real database drivers may be selected and sent a request. The previous
            // fallback used display-only IDs (for example, "DRV-1044"), which are not
            // valid driver primary keys for the API.
            const hasLocalDbDrivers = DB_AVAILABLE_DRIVERS.some(drv =>
                drv.lat && drv.lng && haversineKm(sourcePos, {
                    lat: parseFloat(drv.lat),
                    lng: parseFloat(drv.lng),
                }) < 45
            );

            if (hasLocalDbDrivers) {
                DB_AVAILABLE_DRIVERS.forEach(drv => {
                    if (drv.lat && drv.lng) {
                        const pos = { lat: parseFloat(drv.lat), lng: parseFloat(drv.lng) };
                        candidates.push({
                            id: drv.id,
                            partnerRef: drv.partner_ref || ('DRV-' + (1020 + drv.id)),
                            name: drv.name,
                            vehicle: drv.vehicle_type || drv.vehicle_model || 'Truck',
                            capacity: drv.capacity_tons ? drv.capacity_tons + ' t' : '3.0 t',
                            reg: drv.registration_number || 'MP09 AB 1234',
                            phone: drv.phone || '',
                            pos: pos,
                        });
                    }
                });
            } else {
                const sourceOffsets = [
                    { lat: 0.008, lng: 0.004 },
                    { lat: -0.012, lng: 0.015 },
                    { lat: 0.018, lng: -0.011 },
                    { lat: -0.02, lng: -0.014 },
                    { lat: 0.003, lng: -0.022 },
                ];

                DB_AVAILABLE_DRIVERS.slice(0, count).forEach((drv, index) => {
                    const offset = sourceOffsets[index];
                    candidates.push({
                        id: drv.id,
                        partnerRef: drv.partner_ref || ('DRV-' + (1020 + drv.id)),
                        name: drv.name,
                        vehicle: drv.vehicle_type || drv.vehicle_model || 'Truck',
                        capacity: drv.capacity_tons ? drv.capacity_tons + ' t' : '3.0 t',
                        reg: drv.registration_number || 'Registered',
                        phone: drv.phone || '',
                        pos: {
                            lat: sourcePos.lat + offset.lat,
                            lng: sourcePos.lng + offset.lng,
                        },
                    });
                });
            }

            return candidates.slice(0, count);
        }

        // Fetch driving directions from DirectionsService as a Promise
        function requestDrivingRoute(directionsService, origin, destination) {
            return new Promise((resolve) => {
                directionsService.route({
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING,
                    avoidHighways: false,
                    avoidTolls: false
                }, (result, status) => {
                    if (status === 'OK' && result && result.routes && result.routes[0]) {
                        resolve({ success: true, result: result, route: result.routes[0] });
                    } else {
                        resolve({ success: false, status: status });
                    }
                });
            });
        }

        // Section 2 & 5: Render routes, select nearest truck by driving ETA, configure camera bounds
        async function renderMapAndRoutes(candidates, renderList = true) {
            if (typeof google === 'undefined' || !google.maps) {
                mapEl.textContent = 'Google Maps unavailable. Check API key.';
                return;
            }

            if (!map) {
                map = new google.maps.Map(mapEl, {
                    center: SRC,
                    zoom: 12,
                    styles: MAP_STYLES,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    zoomControl: true,
                });
            } else {
                map.setOptions({ styles: MAP_STYLES });
            }

            clearMapOverlays();

            // 1. Source marker (zIndex: 200)
            const srcMarker = new google.maps.Marker({
                position: SRC,
                map: map,
                title: SRC_NAME,
                icon: createSourceMarkerIcon(),
                zIndex: 200,
            });
            mapOverlays.push(srcMarker);
            mapOverlays.push(addCityNameLabel(SRC, SRC_NAME));

            // 2. Destination marker (zIndex: 200)
            const dstMarker = new google.maps.Marker({
                position: DST,
                map: map,
                title: DST_NAME,
                icon: createDestinationMarkerIcon(),
                zIndex: 200,
            });
            mapOverlays.push(dstMarker);
            mapOverlays.push(addCityNameLabel(DST, DST_NAME));
            const isSameLocation = Math.abs(SRC.lat - DST.lat) < 0.00005 && Math.abs(SRC.lng - DST.lng) < 0.00005;
            if (!isSameLocation) {
            }

            const directionsService = new google.maps.DirectionsService();

            // Fetch Main Delivery Route (SRC -> DST)
            const deliveryRoutePromise = requestDrivingRoute(directionsService, SRC, DST);

            // Fetch routes from SRC to each candidate truck
            const truckRoutePromises = candidates.map(truck => {
                return requestDrivingRoute(directionsService, SRC, truck.pos).then(res => {
                    if (res.success) {
                        const leg = res.route.legs && res.route.legs[0] ? res.route.legs[0] : null;
                        return {
                            truck: truck,
                            success: true,
                            route: res.route,
                            path: extractDetailedPath(res.route),
                            durationValue: leg && leg.duration ? leg.duration.value : 999999,
                            durationText: leg && leg.duration ? leg.duration.text : '--',
                            distanceValue: leg && leg.distance ? leg.distance.value : 999999,
                            distanceText: leg && leg.distance ? leg.distance.text : '--',
                        };
                    } else {
                        // Fallback straight path if road request encounters an issue
                        const straight = [SRC, truck.pos];
                        return {
                            truck: truck,
                            success: false,
                            route: null,
                            path: straight,
                            durationValue: 999999,
                            durationText: '--',
                            distanceValue: Math.round(haversineKm(SRC, truck.pos) * 1000),
                            distanceText: haversineKm(SRC, truck.pos).toFixed(1) + ' km',
                        };
                    }
                });
            });

            const [deliveryResult, truckEvaluations] = await Promise.all([deliveryRoutePromise, Promise.all(truckRoutePromises)]);

            // Section 2: Identify nearest truck by lowest driving ETA (durationValue)
            truckEvaluations.sort((a, b) => {
                if (a.durationValue !== b.durationValue) {
                    return a.durationValue - b.durationValue;
                }
                return a.distanceValue - b.distanceValue;
            });

            // Tag nearest truck vs other 4 available trucks
            truckEvaluations.forEach((evalItem, idx) => {
                evalItem.isNearest = (idx === 0);
                evalItem.rank = idx + 1;
                evalItem.truck.durationText = evalItem.durationText;
                evalItem.truck.distanceText = evalItem.distanceText;
                evalItem.truck.isNearest = (idx === 0);
                evalItem.truck.rank = idx + 1;
            });

            currentDriversList = truckEvaluations.map(e => e.truck);
            if (renderList) renderDriverList(currentDriversList);

            // All points for camera bounds calculation
            const allBoundsPoints = [SRC, DST];

            // Section 3: Route Layering Order
            // Layer 1: Other four truck routes — gray (zIndex: casing 10, line 11)
            const otherTruckEvaluations = truckEvaluations.filter(e => !e.isNearest);
            layerRoutes.others = [];
            otherTruckEvaluations.forEach(item => {
                layerRoutes.others.push(drawCasedRoute(map, item.path, {
                    color: '#929AA8',
                    width: 4,
                    opacity: 0.8,
                    casingWidth: 6,
                    casingOpacity: 0.8,
                    zIndexCasing: 10,
                    zIndexLine: 11
                }));
                item.path.forEach(pt => allBoundsPoints.push(pt));
            });

            // Layer 2: Nearest truck route — black (zIndex: casing 20, line 21)
            const nearestTruckEvaluation = truckEvaluations.find(e => e.isNearest);
            layerRoutes.nearest = [];
            if (nearestTruckEvaluation) {
                layerRoutes.nearest.push(drawCasedRoute(map, nearestTruckEvaluation.path, {
                    color: '#202124',
                    width: 5,
                    opacity: 1,
                    casingWidth: 8,
                    casingOpacity: 1,
                    zIndexCasing: 20,
                    zIndexLine: 21
                }));
                nearestTruckEvaluation.path.forEach(pt => allBoundsPoints.push(pt));
            }

            // Layer 3: Main delivery route — blue (zIndex: casing 30, line 31)
            layerRoutes.delivery = [];
            const deliveryStyle = {
                color: '#1A73E8',
                width: 6,
                opacity: 1,
                casingWidth: 10,
                casingOpacity: 1,
                zIndexCasing: 30,
                zIndexLine: 31
            };
            if (deliveryResult.success) {
                const deliveryPath = extractDetailedPath(deliveryResult.route);
                layerRoutes.delivery.push(drawCasedRoute(map, deliveryPath, deliveryStyle));
                deliveryPath.forEach(pt => allBoundsPoints.push(pt));
            } else {
                const straightDelivery = [SRC, DST];
                layerRoutes.delivery.push(drawCasedRoute(map, straightDelivery, deliveryStyle));
            }

            // Section 6: Marker Design & Placement
            // Nearest truck marker (black truck icon, NEAREST label, prominent, zIndex: 100)
            if (nearestTruckEvaluation) {
                const nTruck = nearestTruckEvaluation.truck;
                const marker = new google.maps.Marker({
                    position: nTruck.pos,
                    map: map,
                    title: nTruck.name + ' (Nearest Available Truck)',
                    icon: createNearestTruckMarkerIcon(),
                    zIndex: 100,
                });
                marker.addListener('click', () => {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(() => marker.setAnimation(null), 700);
                });
                attachTruckInfoWindow(marker, nTruck, true);
                mapOverlays.push(marker);
                truckMarkersMap.set(nTruck.id, marker);
                allBoundsPoints.push(nTruck.pos);
            }

            // Other four truck markers (gray truck icon, smaller, numbered #1..#4, zIndex: 50)
            const driverMarkers = [];
            otherTruckEvaluations.forEach((item, idx) => {
                const truck = item.truck;
                const markerNum = idx + 1;
                const marker = new google.maps.Marker({
                    position: truck.pos,
                    map: map,
                    title: truck.name + ' · Available Truck #' + markerNum,
                    icon: createOtherTruckMarkerIcon(markerNum),
                    zIndex: 50,
                });
                marker.addListener('click', () => {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(() => marker.setAnimation(null), 700);
                });
                attachTruckInfoWindow(marker, truck, false);
                driverMarkers.push(marker);
                truckMarkersMap.set(truck.id, marker);
                allBoundsPoints.push(truck.pos);
            });

            if (driverMarkers.length > 1 && window.markerClusterer?.MarkerClusterer) {
                const customClusterRenderer = {
                    render({ count, position }) {
                        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                            <circle cx="16" cy="16" r="13" fill="#202124" stroke="#FFFFFF" stroke-width="2.5"/>
                            <text x="16" y="20.5" font-family="-apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="800" fill="#FFFFFF" text-anchor="middle">${count}</text>
                        </svg>`;

                        return new google.maps.Marker({
                            position: position,
                            icon: {
                                url: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg),
                                scaledSize: new google.maps.Size(32, 32),
                                anchor: new google.maps.Point(16, 16),
                            },
                            zIndex: 1000 + count,
                        });
                    }
                };

                driverClusterer = new window.markerClusterer.MarkerClusterer({
                    map,
                    markers: driverMarkers,
                    renderer: customClusterRenderer
                });
            } else {
                driverMarkers.forEach(marker => mapOverlays.push(marker));
            }

            // Section 5: Zoom & Camera Configuration
            // Fit bounds to all route geometries and markers with 70px padding, capping max zoom at 14
            applyLayerVisibility();
            fitCameraToBounds(allBoundsPoints);
        }

        // Legend item click = show/hide that route layer on the map
        function applyLayerVisibility() {
            Object.entries(layerRoutes).forEach(([layer, routes]) => {
                routes.forEach(pair => {
                    pair.casing.setVisible(layersVisible[layer]);
                    pair.line.setVisible(layersVisible[layer]);
                });
            });
        }

        document.querySelectorAll('.fd-legend-item').forEach(itemBtn => {
            itemBtn.addEventListener('click', () => {
                const layer = itemBtn.dataset.layer;
                layersVisible[layer] = !layersVisible[layer];
                itemBtn.classList.toggle('is-off', !layersVisible[layer]);
                applyLayerVisibility();
            });
        });

        // Section 7: Marker Selection & Details InfoWindow
        function attachTruckInfoWindow(marker, truck, isNearest) {
            const contentString = `
                <div class="fd-info-window">
                    <div class="fd-info-title">
                        <span>${truck.name}</span>
                        ${isNearest ? '<span class="fd-info-tag">Nearest</span>' : ''}
                    </div>
                    <div class="fd-info-row"><strong>Vehicle:</strong> ${truck.vehicle} · ${truck.capacity}</div>
                    <div class="fd-info-row"><strong>Reg No:</strong> ${truck.reg || 'Registered'}</div>
                    <div class="fd-info-row"><strong>Driver ID:</strong> ${truck.id}</div>
                    <div class="fd-info-metrics">
                        <div>
                            <div class="fd-info-metric-val">${truck.durationText || '--'}</div>
                            <div class="fd-info-metric-lbl">Driving ETA</div>
                        </div>
                        <div>
                            <div class="fd-info-metric-val">${truck.distanceText || '--'}</div>
                            <div class="fd-info-metric-lbl">Road Distance</div>
                        </div>
                    </div>
                </div>
            `;
            const infoWindow = new google.maps.InfoWindow({
                content: contentString,
                pixelOffset: new google.maps.Size(0, isNearest ? -16 : -10)
            });

            marker.addListener('click', () => {
                if (activeInfoWindow) activeInfoWindow.close();
                infoWindow.open(map, marker);
                activeInfoWindow = infoWindow;
                highlightListItem(truck.id);
            });
        }

        // Section 5: Camera fitting with padding and max-zoom 14 guard
        function fitCameraToBounds(points) {
            if (!map || points.length === 0) return;
            const bounds = new google.maps.LatLngBounds();
            points.forEach(pt => {
                if (pt) bounds.extend(pt);
            });
            // Apply 70px viewport padding
            map.fitBounds(bounds, { top: 70, right: 70, bottom: 70, left: 70 });

            // Prevent over-zooming when trucks are geographically close
            const idleListener = google.maps.event.addListener(map, 'idle', () => {
                if (map.getZoom() > 14) {
                    map.setZoom(14);
                }
                google.maps.event.removeListener(idleListener);
            });
        }

        function highlightListItem(truckId) {
            list.querySelectorAll('.fd-item').forEach(el => {
                if (el.dataset.id === truckId) {
                    el.classList.add('fd-item--active');
                    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    el.classList.remove('fd-item--active');
                }
            });
        }

        let popoverHideTimer = null;

        function showDriverPopover(driver, targetElement) {
            const pop = document.getElementById('driver-popover');
            if (!pop || !targetElement) return;

            const popRef = document.getElementById('pop-driver-ref');
            const popName = document.getElementById('pop-driver-name');
            const popPhone = document.getElementById('pop-driver-phone');
            const popVehicle = document.getElementById('pop-driver-vehicle');
            const popReg = document.getElementById('pop-driver-reg');
            const popCapacity = document.getElementById('pop-driver-capacity');
            const popEta = document.getElementById('pop-driver-eta');
            const popDist = document.getElementById('pop-driver-dist');

            if (popRef) popRef.textContent = driver.partnerRef || ('DRV-' + driver.id);
            if (popName) popName.textContent = driver.name || 'Driver Details';
            if (popPhone) popPhone.textContent = driver.phone || 'Phone unavailable';
            if (popVehicle) popVehicle.textContent = driver.vehicle || 'Standard Truck';
            if (popReg) popReg.textContent = driver.reg || driver.registration || 'Registered';
            if (popCapacity) popCapacity.textContent = driver.capacity || '3.0 Tons';
            if (popEta) popEta.textContent = driver.durationText || '--';
            if (popDist) popDist.textContent = driver.distanceText || '--';

            pop.hidden = false;
            pop.classList.add('is-visible');

            const rect = targetElement.getBoundingClientRect();
            const popRect = pop.getBoundingClientRect();

            let top = rect.top + (rect.height / 2) - (popRect.height / 2);
            let left = rect.left - popRect.width - 12;

            if (left < 10) {
                left = rect.right + 12;
            }

            if (top + popRect.height > window.innerHeight - 12) {
                top = window.innerHeight - popRect.height - 12;
            }
            if (top < 12) {
                top = 12;
            }

            pop.style.top = top + 'px';
            pop.style.left = left + 'px';
        }

        function hideDriverPopover() {
            const pop = document.getElementById('driver-popover');
            if (pop) {
                pop.classList.remove('is-visible');
                pop.hidden = true;
            }
        }

        function attachPopoverEvents(element, driverData) {
            element.addEventListener('mouseenter', () => {
                clearTimeout(popoverHideTimer);
                showDriverPopover(driverData, element);
            });
            element.addEventListener('mouseleave', () => {
                popoverHideTimer = setTimeout(hideDriverPopover, 150);
            });
            element.addEventListener('click', (e) => {
                e.stopPropagation();
                clearTimeout(popoverHideTimer);
                showDriverPopover(driverData, element);
            });
        }

        const driverPop = document.getElementById('driver-popover');
        if (driverPop) {
            driverPop.addEventListener('mouseenter', () => clearTimeout(popoverHideTimer));
            driverPop.addEventListener('mouseleave', () => {
                popoverHideTimer = setTimeout(hideDriverPopover, 150);
            });
        }

        function renderDriverList(drivers) {
            list.innerHTML = '';
            drivers.forEach(d => {
                const li = document.createElement('li');
                li.className = 'fd-item' + (d.isNearest ? ' fd-item--nearest' : '') + (selected.has(d.id) ? ' fd-item--selected' : '');
                li.dataset.id = d.id;
                li.innerHTML = `
                    <input type="checkbox" data-id="${d.id}" ${selected.has(d.id) ? 'checked' : ''}>
                    <span class="fd-item__badge ${d.isNearest ? 'fd-item__badge--nearest' : 'fd-item__badge--other'}">
                        ${d.isNearest ? '★' : '#' + d.rank}
                    </span>
                    <div class="fd-item__info">
                        <div class="fd-item__top">
                            <span class="fd-item__name">${d.name}</span>
                            ${d.isNearest ? '<span class="fd-item__tag fd-item__tag--nearest">NEAREST</span>' : ''}
                        </div>
                    </div>
                    <div class="fd-item__metrics">
                        <div class="fd-item__dist fd-item__dist--blue">${d.distanceText}</div>
                    </div>
                    <button type="button" class="fd-item__info-btn" title="View driver full info" aria-label="View driver info">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                    </button>
                `;

                const infoBtn = li.querySelector('.fd-item__info-btn');
                if (infoBtn) {
                    attachPopoverEvents(infoBtn, d);
                }

                li.addEventListener('click', (e) => {
                    if (e.target.closest('.fd-item__info-btn')) {
                        return;
                    }
                    if (e.target.matches('input[type=checkbox]')) return;
                    const cb = li.querySelector('input[type=checkbox]');
                    cb.checked = !cb.checked;
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                });

                list.appendChild(li);
            });
            updateSelectAllButton();
        }

        function renderRecipientList(drivers, title, selectable = false) {
            list.innerHTML = '';
            document.querySelector('.fd-list-head__title').textContent = title;
            drivers.forEach((driver, index) => {
                const li = document.createElement('li');
                li.className = 'fd-item';
                li.innerHTML = `
                    ${selectable ? `<input type="radio" name="confirmed_driver" data-id="${driver.id}" ${index === 0 ? 'checked' : ''}>` : ''}
                    <span class="fd-item__badge fd-item__badge--other">✓</span>
                    <div class="fd-item__info">
                        <div class="fd-item__top"><span class="fd-item__name">${driver.name || 'Driver'}</span></div>
                    </div>
                    <button type="button" class="fd-item__info-btn" title="View driver full info" aria-label="View driver info">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                    </button>`;

                const infoBtn = li.querySelector('.fd-item__info-btn');
                if (infoBtn) {
                    attachPopoverEvents(infoBtn, {
                        id: driver.id || (index + 1),
                        partnerRef: driver.registration ? ('REG-' + driver.registration) : ('DRV-' + (driver.id || index + 1)),
                        name: driver.name || 'Driver',
                        phone: driver.phone || 'Phone unavailable',
                        vehicle: driver.vehicle || 'Vehicle not listed',
                        reg: driver.registration || 'Registered',
                        capacity: 'Standard',
                        durationText: 'Active',
                        distanceText: 'En route'
                    });
                }

                list.appendChild(li);
            });
            if (!drivers.length) list.innerHTML = '<li class="fd-item">No driver information is available yet.</li>';
        }

        function resolveLocation(address, city, cb) {
            const combinedKey = address ? (address + ', ' + city).trim() : city;
            if (locationCoords[combinedKey]) return cb(locationCoords[combinedKey]);
            if (address && locationCoords[address]) return cb(locationCoords[address]);
            if (city && locationCoords[city]) return cb(locationCoords[city]);

            if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                new google.maps.Geocoder().geocode({ address: combinedKey + ', India' }, (res, status) => {
                    if (status === 'OK' && res[0]) {
                        cb({ lat: res[0].geometry.location.lat(), lng: res[0].geometry.location.lng() });
                    } else {
                        cb(null);
                    }
                });
            } else {
                cb(null);
            }
        }

        function formatLoad(weight) {
            const kg = Number(String(weight || '').replace(/,/g, ''));
            if (!kg) return '';
            if (kg >= 1000) {
                const tons = kg / 1000;
                return (Number.isInteger(tons) ? tons : tons.toFixed(1)) + ' ton';
            }
            return kg + ' kg';
        }

        function openModal(req) {
            currentActiveRequest = req;
            window.currentActiveRequest = req;
            if (detailsPanel && modal.parentElement !== detailsPanel) detailsPanel.appendChild(modal);
            if (detailEmpty) detailEmpty.hidden = true;
            document.querySelectorAll('.request-row.is-selected').forEach(card => card.classList.remove('is-selected'));
            req.cardEl?.classList.add('is-selected');
            const isRecipientView = Array.isArray(req.recipientDrivers);
            const confirmMode = req.status === 'driver_accepted';
            const footer = modal.querySelector('.fd-footer');
            footer.hidden = isRecipientView && !confirmMode;
            sendBtn.textContent = confirmMode ? 'Confirm Driver' : 'Send Request';
            document.querySelector('.fd-list-head__title').textContent = isRecipientView ? (req.recipientTitle || 'Drivers notified') : 'Select All';

            // Populate Request Details Panel
            if (fdTag) fdTag.textContent = '';
            if (fdInfoStatus) {
                const statusMap = {
                    'new': { text: 'New Request', class: 'status-badge--new' },
                    'ready_to_match': { text: 'New Request', class: 'status-badge--new' },
                    'invitations_sent': { text: 'Request Sent to Driver', class: 'status-badge--sent' },
                    'awaiting_responses': { text: 'Request Sent to Driver', class: 'status-badge--sent' },
                    'driver_accepted': { text: 'Driver Accepts', class: 'status-badge--accepted' },
                    'follow_up_needed': { text: 'Driver Accepts', class: 'status-badge--accepted' },
                    'confirmed': { text: 'Active (Confirmed)', class: 'status-badge--confirmed' },
                    'closed': { text: 'Completed', class: 'status-badge--closed' },
                    'cancelled': { text: 'Cancelled', class: 'status-badge--closed' }
                };

                const currentInfo = statusMap[req.status] || {
                    text: (req.status || 'New Request').replace(/_/g, ' '),
                    class: 'status-badge--new'
                };

                fdInfoStatus.textContent = currentInfo.text;
                fdInfoStatus.className = 'status-badge ' + currentInfo.class;
            }

            const isConfirmedOrClosed = ['confirmed', 'closed', 'completed'].includes(req.status);
            const candidateSection = document.getElementById('fd-candidate-drivers-section');
            const confirmedSection = document.getElementById('fd-confirmed-driver-section');

            if (isConfirmedOrClosed) {
                if (candidateSection) candidateSection.hidden = true;
                if (confirmedSection) confirmedSection.hidden = false;

                const confDriver = (Array.isArray(req.recipientDrivers) && req.recipientDrivers.length > 0)
                    ? req.recipientDrivers[0]
                    : { name: 'Suresh Patel', phone: '+91 98263 11001', vehicle: 'Tata 407', registration: 'MP09 AB 1234' };

                const confName = document.getElementById('fd-conf-driver-name');
                const confPhone = document.getElementById('fd-conf-driver-phone');
                const confVehicle = document.getElementById('fd-conf-driver-vehicle');
                const confReg = document.getElementById('fd-conf-driver-reg');

                if (confName) confName.textContent = confDriver.name || 'Suresh Patel';
                if (confPhone) confPhone.textContent = confDriver.phone || '+91 98263 11001';
                if (confVehicle) confVehicle.textContent = confDriver.vehicle || 'Tata 407';
                if (confReg) confReg.textContent = confDriver.registration || confDriver.reg || 'MP09 AB 1234';
            } else {
                if (candidateSection) candidateSection.hidden = false;
                if (confirmedSection) confirmedSection.hidden = true;
            }
            if (fdInfoPickupCity) fdInfoPickupCity.textContent = req.pickupCity || '-';
            if (fdInfoPickupAddress) fdInfoPickupAddress.textContent = req.pickupAddress || '-';
            if (fdInfoDstCity) fdInfoDstCity.textContent = req.destinationCity || '-';
            if (fdInfoDstAddress) fdInfoDstAddress.textContent = req.destinationAddress || '-';
            if (fdInfoCustomer) fdInfoCustomer.textContent = req.customer || '-';
            if (fdInfoPhone) fdInfoPhone.textContent = req.phone || '-';
            if (fdInfoEmail) {
                fdInfoEmail.textContent = req.email || '-';
                const emailField = fdInfoEmail.closest('.fd-info-field');
                if (emailField) emailField.style.display = req.email ? '' : 'none';
            }
            if (fdInfoGoods) fdInfoGoods.textContent = req.goods || '-';
            if (fdInfoLoad) fdInfoLoad.textContent = formatLoad(req.weight) || '-';
            if (fdInfoVehicle) fdInfoVehicle.textContent = req.vehicle || 'Standard Truck';
            if (fdInfoDate) fdInfoDate.textContent = req.date || 'Immediate';
            if (fdInfoFragile) fdInfoFragile.textContent = req.fragile || 'No';
            if (fdInfoLoading) fdInfoLoading.textContent = req.loading || 'Not required';

            if (fdInfoDescWrap) {
                if (req.goodsDesc) {
                    fdInfoDescWrap.style.display = 'flex';
                    if (fdInfoDesc) fdInfoDesc.textContent = req.goodsDesc;
                } else {
                    fdInfoDescWrap.style.display = 'none';
                }
            }

            if (fdInfoInstructionsWrap) {
                if (req.instructions) {
                    fdInfoInstructionsWrap.style.display = 'flex';
                    if (fdInfoInstructions) fdInfoInstructions.textContent = req.instructions;
                } else {
                    fdInfoInstructionsWrap.style.display = 'none';
                }
            }

            sub.textContent = req.pickupAddress + ' (' + req.pickupCity + ') → ' + req.destinationAddress + ' (' + req.destinationCity + ') · ' + req.goods + ' · ' + formatLoad(req.weight);
            modal.hidden = false;
            selected.clear();

            if (typeof google === 'undefined' || !google.maps) {
                mapEl.textContent = 'Loading Google Maps…';
                window.__fdPending = () => openModal(req);
                return;
            }

            resolveLocation(req.pickupAddress, req.pickupCity, srcPos => {
                SRC = srcPos || locationCoords['Indore'];
                SRC_NAME = req.pickupCity || req.pickupAddress || 'Source';
                resolveLocation(req.destinationAddress, req.destinationCity, dstPos => {
                    DST = dstPos || locationCoords['Transport Nagar, Indore'] || SRC;
                    DST_NAME = req.destinationCity || req.destinationAddress || 'Destination';
                    const candidates = isRecipientView ? [] : getCandidateDrivers(SRC);
                    renderMapAndRoutes(candidates, !isRecipientView);
                    if (isRecipientView) {
                        renderRecipientList(req.recipientDrivers, req.recipientTitle, confirmMode);
                        if (confirmMode && req.recipientDrivers.length) {
                            selected.add(String(req.recipientDrivers[0].id));
                            updateSelectedCount();
                        }
                    }
                    updateSelectedCount();
                });
            });
        }

        function closeModal() {
            modal.hidden = true;
            if (activeInfoWindow) {
                activeInfoWindow.close();
                activeInfoWindow = null;
            }
        }

        document.querySelectorAll('.request-row').forEach(row => {
            const triggerHandler = () => {
                openModal({
                    id: row.dataset.requestId,
                    dbId: row.dataset.dbId,
                    status: row.dataset.status,
                    pickupCity: row.dataset.pickup,
                    pickupAddress: row.dataset.pickupAddress,
                    destinationCity: row.dataset.destination,
                    destinationAddress: row.dataset.destinationAddress,
                    goods: row.dataset.goods,
                    goodsDesc: row.dataset.goodsDesc,
                    weight: row.dataset.weight,
                    quantity: row.dataset.quantity,
                    customer: row.dataset.customer,
                    phone: row.dataset.phone,
                    email: row.dataset.email,
                    vehicle: row.dataset.vehicle,
                    fragile: row.dataset.fragile,
                    loading: row.dataset.loading,
                    instructions: row.dataset.instructions,
                    date: row.dataset.date,
                    recipientDrivers: ['new', 'ready_to_match'].includes(row.dataset.status)
                        ? undefined
                        : (row.dataset.status === 'driver_accepted'
                            ? (JSON.parse(row.dataset.acceptedDrivers || '[]').length ? JSON.parse(row.dataset.acceptedDrivers || '[]') : [{ id: 1, name: 'Suresh Patel', phone: '+91 98263 11001', vehicle: 'Tata 407', registration: 'MP09 AB 1234' }])
                            : (['invitations_sent', 'awaiting_responses'].includes(row.dataset.status)
                                ? JSON.parse(row.dataset.sentDrivers || '[]')
                                : (['confirmed', 'closed'].includes(row.dataset.status)
                                    ? JSON.parse(row.dataset.confirmedDrivers || '[]')
                                    : undefined))),
                    recipientTitle: ['new', 'ready_to_match'].includes(row.dataset.status)
                        ? 'Available Drivers Nearby'
                        : (row.dataset.status === 'driver_accepted'
                            ? 'Drivers who accepted'
                            : (['confirmed', 'closed'].includes(row.dataset.status) ? 'Confirmed driver' : 'Drivers notified')),
                    cardEl: row
                });
            };

            row.addEventListener('click', triggerHandler);
            row.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    triggerHandler();
                }
            });
        });

        modal.querySelectorAll('[data-fd-close]').forEach(b => b.addEventListener('click', closeModal));

        document.getElementById('fd-select-all').addEventListener('click', () => {
            const boxes = Array.from(list.querySelectorAll('input[type=checkbox]'));
            const allSelected = boxes.length > 0 && boxes.every(cb => cb.checked);
            boxes.forEach(cb => {
                cb.checked = !allSelected;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        list.addEventListener('change', e => {
            if (e.target.matches('input[type=checkbox], input[type=radio]')) {
                const li = e.target.closest('.fd-item');
                if (e.target.checked) {
                    if (e.target.type === 'radio') selected.clear();
                    selected.add(e.target.dataset.id);
                    if (li) li.classList.add('fd-item--selected');
                } else {
                    selected.delete(e.target.dataset.id);
                    if (li) li.classList.remove('fd-item--selected');
                }
                updateSelectedCount();
                updateSelectAllButton();
            }
        });

        function updateSelectAllButton() {
            const boxes = Array.from(list.querySelectorAll('input[type=checkbox]'));
            const allSelected = boxes.length > 0 && boxes.every(cb => cb.checked);
            document.getElementById('fd-select-all').classList.toggle('is-all-selected', allSelected);
        }

        function updateSelectedCount() {
            if (selectedEl) selectedEl.textContent = selected.size + ' selected';
            sendBtn.disabled = selected.size === 0;
        }

        function moveCardToSentColumn(cardEl, notifiedDrivers = []) {
            if (!cardEl) return;
            cardEl.dataset.status = 'awaiting_responses';
            cardEl.dataset.sentDrivers = JSON.stringify(notifiedDrivers);
            showRequestTab('new_requests', 'awaiting_responses');
            cardEl.click();
        }

        sendBtn.addEventListener('click', () => {
            const activeReq = currentActiveRequest;
            if (activeReq?.status === 'driver_accepted') {
                const driverId = Array.from(selected)[0];
                if (!driverId) return;

                sendBtn.disabled = true;
                sendBtn.textContent = 'Confirming…';
                fetch(`/requests/${activeReq.dbId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: 'confirmed', driver_id: driverId })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Unable to confirm the driver.');
                    window.location.reload();
                })
                .catch(error => {
                    toastText.textContent = error.message || 'Unable to confirm the driver.';
                    toast.hidden = false;
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Confirm Driver';
                });
                return;
            }

            const n = selected.size;
            if (n === 0) return;

            const driverIds = Array.from(selected).map(id => parseInt(id, 10));
            const notifiedDrivers = currentDriversList
                .filter(driver => selected.has(String(driver.id)))
                .map(driver => ({
                    id: driver.id,
                    name: driver.name,
                    phone: driver.phone,
                    vehicle: driver.vehicle,
                    registration: driver.reg,
                }));
            sendBtn.disabled = true;
            const prevText = sendBtn.textContent;
            sendBtn.textContent = 'Sending…';

            const dbId = activeReq ? activeReq.dbId : null;

            if (dbId) {
                fetch(`/requests/${dbId}/send-drivers`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ driver_ids: driverIds })
                })
                .then(async r => {
                    const data = await r.json();
                    if (!r.ok) {
                        throw new Error(data.message || 'Unable to send the request.');
                    }
                    return data;
                })
                .then(data => {
                    toastText.textContent = data.message || ('Request sent to ' + n + ' driver' + (n === 1 ? '' : 's'));
                    toast.hidden = false;
                    if (activeReq && activeReq.cardEl) {
                        moveCardToSentColumn(activeReq.cardEl, notifiedDrivers);
                    }
                    if (activeReq) {
                        activeReq.recipientDrivers = notifiedDrivers;
                        activeReq.recipientTitle = 'Drivers notified';
                        activeReq.status = 'awaiting_responses';
                        const footer = modal.querySelector('.fd-footer');
                        if (footer) footer.hidden = true;
                        renderRecipientList(notifiedDrivers, 'Drivers notified');
                        selected.clear();
                        updateSelectedCount();
                    }
                    setTimeout(() => { toast.hidden = true; }, 4000);
                })
                .catch(err => {
                    console.error(err);
                    toastText.textContent = err.message || 'Unable to send the request. Please try again.';
                    toast.hidden = false;
                })
                .finally(() => {
                    sendBtn.textContent = prevText;
                    setTimeout(() => { toast.hidden = true; }, 4000);
                });
            } else {
                toastText.textContent = 'Request sent to ' + n + ' driver' + (n === 1 ? '' : 's');
                toast.hidden = false;
                if (activeReq && activeReq.cardEl) {
                    moveCardToSentColumn(activeReq.cardEl, notifiedDrivers);
                }
                sendBtn.textContent = prevText;
                if (activeReq) {
                    activeReq.recipientDrivers = notifiedDrivers;
                    activeReq.recipientTitle = 'Drivers notified';
                    activeReq.status = 'awaiting_responses';
                    const footer = modal.querySelector('.fd-footer');
                    if (footer) footer.hidden = true;
                    renderRecipientList(notifiedDrivers, 'Drivers notified');
                    selected.clear();
                    updateSelectedCount();
                }
                setTimeout(() => { toast.hidden = true; }, 4000);
            }
        });

        toast.querySelector('[data-toast-close]').addEventListener('click', () => { toast.hidden = true; });

        const legendToggle = document.getElementById('fd-legend-toggle');
        const legend = document.getElementById('fd-legend');
        legendToggle.addEventListener('click', () => {
            const collapsed = legend.hasAttribute('data-collapsed');
            if (collapsed) { legend.removeAttribute('data-collapsed'); legendToggle.setAttribute('aria-expanded', 'true'); }
            else { legend.setAttribute('data-collapsed', ''); legendToggle.setAttribute('aria-expanded', 'false'); }
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modal.hidden) closeModal();
        });

        function updateTabCounts() {
            document.querySelectorAll('.req-stepper__tab, .req-pill-tab').forEach(tab => {
                const key = tab.dataset.requestTab;
                const count = document.querySelectorAll(`.req-sidebar__group[data-column="${key}"] .request-row`).length;
                const badge = tab.querySelector('span');
                if (badge) badge.textContent = count;
            });
            updateStepperGlider();
        }

        const masterStatusBtn = document.getElementById('req-master-status-btn');
        const masterStatusDialog = document.getElementById('master-status-dialog');
        const masterStatusForm = document.getElementById('master-status-form');
        const masterStatusRef = document.getElementById('master-status-req-ref');

        if (masterStatusBtn && masterStatusDialog) {
            masterStatusBtn.addEventListener('click', () => {
                const activeCard = document.querySelector('.req-sidebar__group.is-visible .request-row.is-selected')
                    || document.querySelector('.req-sidebar__group.is-visible .request-row:not(.is-stage-hidden)')
                    || document.querySelector('.request-row.is-selected')
                    || document.querySelector('.request-row');

                const targetId = currentActiveRequest?.dbId || activeCard?.dataset.dbId;
                if (!targetId) {
                    window.alert('Select a request to change status.');
                    return;
                }

                const card = document.querySelector(`.request-row[data-db-id="${targetId}"]`);
                const currentStatus = currentActiveRequest?.status || card?.dataset.status || 'new';
                const ref = currentActiveRequest?.id || card?.dataset.requestId || 'Request';
                const customer = card?.dataset.customer || '';

                if (masterStatusRef) {
                    masterStatusRef.textContent = ref + (customer ? ` (${customer})` : '');
                }
                masterStatusForm.dataset.targetId = targetId;

                const radio = masterStatusForm.querySelector(`input[name="master_status"][value="${currentStatus}"]`)
                    || masterStatusForm.querySelector('input[name="master_status"][value="new"]');
                if (radio) radio.checked = true;

                if (typeof masterStatusDialog.showModal === 'function') {
                    masterStatusDialog.showModal();
                } else {
                    masterStatusDialog.setAttribute('open', '');
                }
            });

            masterStatusDialog.querySelectorAll('[data-master-status-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (typeof masterStatusDialog.close === 'function') masterStatusDialog.close();
                    else masterStatusDialog.removeAttribute('open');
                });
            });

            masterStatusForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const selectedRadio = masterStatusForm.querySelector('input[name="master_status"]:checked');
                if (!selectedRadio) return;

                const targetId = masterStatusForm.dataset.targetId;
                const newStatus = selectedRadio.value;
                const submitBtn = masterStatusForm.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Updating…';

                try {
                    const response = await fetch(`/requests/${targetId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Unable to update status.');

                    const card = document.querySelector(`.request-row[data-db-id="${targetId}"]`);
                    let destTab = 'new_requests';
                    let destStage = 'new';

                    if (newStatus === 'new') {
                        destTab = 'new_requests'; destStage = 'new';
                    } else if (newStatus === 'awaiting_responses') {
                        destTab = 'new_requests'; destStage = 'awaiting_responses';
                    } else if (newStatus === 'driver_accepted') {
                        destTab = 'new_requests'; destStage = 'driver_accepted';
                    } else if (newStatus === 'confirmed') {
                        destTab = 'active_requests'; destStage = 'confirmed';
                    } else if (newStatus === 'closed') {
                        destTab = 'completed_requests'; destStage = 'closed';
                    }

                    if (card) {
                        card.dataset.status = newStatus;
                        if (newStatus === 'new') {
                            card.dataset.sentDrivers = '[]';
                            card.dataset.acceptedDrivers = '[]';
                            card.dataset.confirmedDrivers = '[]';
                        } else if (newStatus === 'driver_accepted') {
                            const suresh = DB_AVAILABLE_DRIVERS.find(d => d.name === 'Suresh Patel') || {
                                id: 1,
                                name: 'Suresh Patel',
                                phone: '+91 98263 11001',
                                vehicle_type: 'Tata 407',
                                registration_number: 'MP09 AB 1234'
                            };
                            card.dataset.acceptedDrivers = JSON.stringify([{
                                id: suresh.id,
                                name: suresh.name,
                                phone: suresh.phone || '+91 98263 11001',
                                vehicle: suresh.vehicle_type || 'Tata 407',
                                registration: suresh.registration_number || 'MP09 AB 1234'
                            }]);
                        }
                        const destGroup = document.querySelector(`.req-sidebar__group[data-column="${destTab}"]`);
                        if (destGroup && card.parentElement !== destGroup) {
                            destGroup.prepend(card);
                        }
                    }

                    if (typeof masterStatusDialog.close === 'function') masterStatusDialog.close();
                    else masterStatusDialog.removeAttribute('open');

                    updateTabCounts();
                    showRequestTab(destTab, destStage);
                    if (card) {
                        card.click();
                    }
                } catch (error) {
                    window.alert(error.message || 'Unable to update status.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        }

        const resetBtn = document.getElementById('req-reset-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', async () => {
                if (!window.confirm('Reset this request back to New Request?')) return;
                resetBtn.disabled = true;
                const originalHtml = resetBtn.innerHTML;
                resetBtn.innerHTML = 'Resetting…';

                try {
                    const activeCard = document.querySelector('.req-sidebar__group.is-visible .request-row.is-selected')
                        || document.querySelector('.req-sidebar__group.is-visible .request-row:not(.is-stage-hidden)')
                        || document.querySelector('.request-row.is-selected')
                        || document.querySelector('.request-row');

                    const targetId = currentActiveRequest?.dbId || activeCard?.dataset.dbId;
                    if (!targetId) {
                        throw new Error('Select a request to reset.');
                    }

                    const response = await fetch(`/requests/${targetId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ status: 'new' }),
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Unable to reset request.');

                    const card = document.querySelector(`.request-row[data-db-id="${targetId}"]`);
                    if (card) {
                        card.dataset.status = 'new';
                        card.dataset.sentDrivers = '[]';
                        card.dataset.acceptedDrivers = '[]';
                        card.dataset.confirmedDrivers = '[]';
                        const newReqGroup = document.querySelector('.req-sidebar__group[data-column="new_requests"]');
                        if (newReqGroup && card.parentElement !== newReqGroup) {
                            newReqGroup.prepend(card);
                        }
                    }

                    updateTabCounts();
                    showRequestTab('new_requests');
                    if (card) {
                        card.click();
                    }
                } catch (error) {
                    window.alert(error.message || 'Unable to reset request.');
                } finally {
                    resetBtn.disabled = false;
                    resetBtn.innerHTML = originalHtml;
                }
            });
        }
    })();
    </script>
    @if (env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY'))
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY') }}&callback=initFindDriversMap"></script>
    @endif
    <script>
    (function () {
        const searchForm = document.getElementById('req-search');
        const searchInput = document.getElementById('req-search-input');
        const acceptedDialog = document.getElementById('accepted-drivers-dialog');
        const acceptedForm = document.getElementById('accepted-drivers-form');
        const acceptedList = document.getElementById('accepted-drivers-list');

        function openDialog(dialog, anchor) {
            const rect = anchor.getBoundingClientRect();
            if (typeof dialog.showModal === 'function') {
                dialog.style.top = (rect.bottom + 8) + 'px';
                dialog.style.right = (window.innerWidth - rect.right) + 'px';
                dialog.showModal();
            } else {
                dialog.setAttribute('open', '');
            }
        }

        window.openAcceptedDrivers = (card) => {
            const drivers = JSON.parse(card.dataset.acceptedDrivers || '[]');
            if (!drivers.length && card.dataset.acceptedDemo === 'true') {
                drivers.push(
                    ...JSON.parse(card.dataset.sentDrivers || '[]').map(driver => ({ ...driver, demo: true })),
                );
            }
            acceptedForm.dataset.requestId = card.dataset.dbId;
            acceptedDialog.querySelector('.req-dialog__title').textContent = 'Drivers who accepted';
            acceptedForm.querySelector('[type="submit"]').hidden = false;
            acceptedList.innerHTML = drivers.length
                ? drivers.map((driver, index) => `
                    <label class="accepted-driver-option">
                        <input type="radio" name="accepted_driver_id" value="${driver.id || ''}" data-demo="${driver.demo ? 'true' : 'false'}" ${index === 0 ? 'checked' : ''}>
                        <span class="accepted-driver-option__details">
                            <strong>${driver.name || 'Driver'}</strong>
                            <span class="accepted-driver-option__phone">${driver.phone || 'Phone unavailable'}</span>
                            <span class="accepted-driver-option__meta">${driver.vehicle || 'Vehicle not listed'}${driver.registration ? ' · ' + driver.registration : ''}</span>
                        </span>
                    </label>`).join('')
                : '<p class="cell-muted">No drivers have accepted this request yet.</p>';
            openDialog(acceptedDialog, card);
        };

        window.openSentDrivers = (card) => {
            const drivers = JSON.parse(card.dataset.sentDrivers || '[]');
            acceptedDialog.querySelector('.req-dialog__title').textContent = 'Request sent to';
            acceptedForm.querySelector('[type="submit"]').hidden = true;
            acceptedList.innerHTML = drivers.length
                ? drivers.map(driver => `
                    <div class="accepted-driver-option">
                        <span class="accepted-driver-option__details">
                            <strong>${driver.name || 'Driver'}</strong>
                            <span class="accepted-driver-option__phone">${driver.phone || 'Phone unavailable'}</span>
                            <span class="accepted-driver-option__meta">${driver.vehicle || 'Vehicle not listed'}${driver.registration ? ' · ' + driver.registration : ''}</span>
                        </span>
                    </div>`).join('')
                : '<p class="cell-muted">No drivers have been notified yet.</p>';
            openDialog(acceptedDialog, card);
        };

        if (searchForm && searchInput) {
            const searchSubmitBtn = searchForm.querySelector('.req-search__btn');
            if (searchSubmitBtn) {
                searchSubmitBtn.addEventListener('click', e => {
                    if (!searchForm.classList.contains('is-open')) {
                        e.preventDefault();
                        searchForm.classList.add('is-open');
                        searchInput.focus();
                        return;
                    }
                    if (searchInput.value.trim() === '') {
                        e.preventDefault();
                        searchForm.classList.remove('is-open');
                        searchInput.blur();
                    }
                });
            }

            searchInput.addEventListener('keydown', e => {
                if (e.key === 'Enter' && searchForm.classList.contains('is-open')) {
                    if (searchInput.value.trim() !== '') {
                        searchForm.submit();
                    }
                }
            });
        }

        acceptedDialog.querySelectorAll('[data-accepted-close]').forEach(button => {
            button.addEventListener('click', () => acceptedDialog.close());
        });

        acceptedForm.addEventListener('submit', async e => {
            e.preventDefault();
            const selectedDriver = acceptedForm.querySelector('input[name="accepted_driver_id"]:checked');
            if (!selectedDriver) return;
            const submit = acceptedForm.querySelector('[type="submit"]');
            submit.disabled = true;
            try {
                const response = await fetch(`/requests/${acceptedForm.dataset.requestId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ status: 'confirmed', driver_id: selectedDriver.dataset.demo === 'true' ? null : (selectedDriver.value || null) }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Unable to confirm the driver.');
                window.location.reload();
            } catch (error) {
                window.alert(error.message || 'Unable to confirm the driver.');
            } finally {
                submit.disabled = false;
            }
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (acceptedDialog.open) {
                    acceptedDialog.close();
                    return;
                }
            }
        });
    })();
    <script>
    function selectInitialRequest() {
        const first = document.querySelector('.req-sidebar__group.is-visible .request-row')
            || document.querySelector('.req-sidebar__group[data-column="new_requests"] .request-row')
            || document.querySelector('.request-row');
        if (first) first.click();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', selectInitialRequest);
    } else {
        selectInitialRequest();
    }
    window.addEventListener('load', function() {
        setTimeout(selectInitialRequest, 50);
    });
    </script>
@endsection
