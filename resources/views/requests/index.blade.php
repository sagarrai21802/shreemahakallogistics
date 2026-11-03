@extends('layouts.app')

@section('title', 'Transport Requests')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Transport Requests</h1>
        </div>

        <div class="req-actions">
            <button type="button" id="req-status-btn" class="req-status-btn" aria-haspopup="dialog">
                Manage Status
            </button>
            <form id="req-search" class="req-search {{ $q !== '' ? 'is-open' : '' }}" method="GET" action="{{ route('requests.index') }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="req-search__field">
                    <input
                        id="req-search-input"
                        class="req-search__input"
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Reference, customer or city"
                        autocomplete="off"
                    >
                    <button type="submit" class="req-search__btn {{ $q !== '' ? 'is-active' : '' }}" aria-label="Search" title="Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                            <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </form>

            <button type="button" id="req-filters-btn" class="req-icon-btn {{ ($q !== '' || $status !== '') ? 'is-active' : '' }}" aria-haspopup="dialog" title="Filters">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </header>

    <dialog id="req-filters-dialog" class="req-dialog">
        <form method="GET" action="{{ route('requests.index') }}">
            <input type="hidden" name="q" value="{{ $q }}">
            <div class="req-dialog__head">
                <span class="req-dialog__title">Filters</span>
                <button type="button" class="req-dialog__close" data-dialog-close aria-label="Close">×</button>
            </div>
            <div class="req-dialog__body">
                <label class="req-dialog__label" for="dialog-status">Status</label>
                <select class="req-dialog__select" id="dialog-status" name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="req-dialog__foot">
                <a class="btn btn--quiet" href="{{ route('requests.index') }}">Clear</a>
                <button class="btn btn--primary" type="submit">Apply</button>
            </div>
        </form>
    </dialog>

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

    <dialog id="req-status-dialog" class="req-dialog req-status-dialog">
        <form id="req-status-form">
            <div class="req-dialog__head">
                <span class="req-dialog__title">Manage Request Status</span>
                <button type="button" class="req-dialog__close" data-status-dialog-close aria-label="Close">×</button>
            </div>
            <div class="req-dialog__body">
                <label class="req-dialog__label" for="status-request">Request</label>
                <select class="req-dialog__select" id="status-request" name="request_id" required>
                    @foreach ($requests as $request)
                        <option value="{{ $request->id }}">{{ $request->reference }} · {{ $request->customer_name }}</option>
                    @endforeach
                </select>

                <label class="req-dialog__label" for="status-value">Move to</label>
                <select class="req-dialog__select" id="status-value" name="status" required>
                    <option value="new">New Request (reset)</option>
                    <option value="awaiting_responses">Request Sent to Drivers</option>
                    <option value="driver_accepted">Accepted (In Progress)</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="closed">Journey Completed</option>
                </select>
            </div>
            <div class="req-dialog__foot">
                <button type="button" class="btn btn--quiet" data-status-dialog-close>Cancel</button>
                <button class="btn btn--primary" type="submit">Update Status</button>
            </div>
        </form>
    </dialog>

    @php
        $columns = [
            'new' => [
                'title' => 'New Request',
                'statuses' => ['new', 'ready_to_match'],
            ],
            'sent' => [
                'title' => 'Request Sent to Drivers',
                'statuses' => ['invitations_sent', 'awaiting_responses'],
            ],
            'in_progress' => [
                'title' => 'Drivers Accepted the Request',
                'statuses' => ['driver_accepted', 'follow_up_needed'],
            ],
            'confirmed' => [
                'title' => 'Confirmed',
                'statuses' => ['confirmed'],
            ],
            'journey_completed' => [
                'title' => 'Journey Completed',
                'statuses' => ['closed', 'cancelled', 'no_driver_available'],
            ],
        ];

        $groupedRequests = [
            'new' => collect(),
            'sent' => collect(),
            'in_progress' => collect(),
            'confirmed' => collect(),
            'journey_completed' => collect(),
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
                $groupedRequests['new']->push($req);
            }
        }
    @endphp

    <section class="kanban-section" aria-label="Transport requests kanban board">
        @if ($requests->isEmpty())
            <div class="panel empty-state">
                <p class="empty-state__title">No requests found</p>
                <p class="empty-state__text">
                    @if ($q !== '' || $status !== '')
                        No requests match these filters.
                        <a class="text-link" href="{{ route('requests.index') }}">Clear filters</a>
                    @else
                        New customer requests will appear here as they arrive.
                    @endif
                </p>
            </div>
        @else
            <div class="kanban-board">
                @foreach ($columns as $colKey => $col)
                    @php
                        $items = $groupedRequests[$colKey];
                    @endphp
                    <div class="kanban-column" data-column="{{ $colKey }}" id="kanban-col-{{ $colKey }}">
                        <div class="kanban-column__head">
                            <div class="kanban-column__title-wrap">
                                <h2 class="kanban-column__title">{{ $col['title'] }}</h2>
                            </div>
                            <span class="kanban-column__count">{{ $items->count() }}</span>
                        </div>

                        <div class="kanban-column__body">
                            @forelse ($items as $request)
                                <div class="kanban-card request-row"
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
                                    data-accepted-drivers="{{ $request->invitations->where('response_status', 'accepted')->map(fn ($invitation) => ['id' => $invitation->driver_id, 'name' => $invitation->driver?->name, 'phone' => $invitation->driver?->phone, 'vehicle' => $invitation->driver?->vehicle_type, 'registration' => $invitation->driver?->registration_number])->values()->toJson() }}"
                                    data-sent-drivers="{{ $request->invitations->map(fn ($invitation) => ['name' => $invitation->driver?->name, 'phone' => $invitation->driver?->phone, 'vehicle' => $invitation->driver?->vehicle_type, 'registration' => $invitation->driver?->registration_number])->values()->toJson() }}"
                                    data-confirmed-drivers="{{ $request->invitations->where('response_status', 'accepted')->map(fn ($invitation) => ['name' => $invitation->driver?->name, 'phone' => $invitation->driver?->phone, 'vehicle' => $invitation->driver?->vehicle_type, 'registration' => $invitation->driver?->registration_number])->values()->toJson() }}"
                                    data-accepted-demo="{{ $request->reference === 'TRQ-2026-0143' ? 'true' : 'false' }}"
                                    draggable="true"
                                    role="button"
                                    tabindex="0">
                                    <div class="kanban-card__top">
                                        <div class="kanban-card__identity">
                                            <span class="kanban-card__ref">{{ $request->reference }}</span>
                                            <span class="kanban-card__customer">{{ $request->customer_name }}</span>
                                        </div>
                                    <x-status-badge :status="$request->status" class="kanban-card__badge" />
                                    </div>

                                    <div class="kanban-card__route">
                                        <div class="kanban-card__route-cities">
                                            <span class="kanban-card__city">{{ $request->pickup_city }}</span>
                                            <span class="kanban-card__arrow" aria-hidden="true">→</span>
                                            <span class="kanban-card__city">{{ $request->destination_city }}</span>
                                        </div>
                                        <span class="kanban-card__sub">{{ $request->pickup_address }} to {{ $request->destination_address }}</span>
                                    </div>

                                    <div class="kanban-card__footer">
                                        <div class="kanban-card__meta-item">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                                            </svg>
                                            <span class="kanban-card__goods-label" title="{{ $request->goods_description }}">
                                                {{ $request->goods_category }}@if ($request->is_fragile) · Fragile @endif
                                            </span>
                                        </div>
                                        <div class="kanban-card__load">
                                            <span class="kanban-card__weight">{{ number_format($request->weight_kg) }} kg</span>
                                            <span class="kanban-card__dot">·</span>
                                            <span class="kanban-card__qty">{{ $request->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="kanban-column__empty">
                                    <span>No requests</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    <!-- Find Drivers modal -->
    <div id="findDriversModal" class="fd-overlay" hidden>
        <div class="fd-modal" role="dialog" aria-modal="true" aria-labelledby="fd-title">
            <header class="fd-modal__head">
                <div class="fd-modal__head-info">
                    <span class="fd-modal__tag" id="fd-tag">Request</span>
                    <h2 id="fd-title" class="fd-modal__title">Find Drivers & Assign</h2>
                    <p class="fd-modal__sub" id="fd-sub">Select available nearby drivers</p>
                </div>
                <div class="fd-modal__head-actions">
                    <div class="fd-toolbar-inline">
                        <label class="fd-label" for="fd-count">Consider:</label>
                        <select id="fd-count" class="fd-select">
                            <option value="5" selected>5 drivers</option>
                            <option value="10">10 drivers</option>
                            <option value="15">15 drivers</option>
                            <option value="20">20 drivers</option>
                            <option value="25">25 drivers</option>
                            <option value="30">30 drivers</option>
                        </select>
                    </div>
                    <button type="button" class="fd-close" data-fd-close aria-label="Close">×</button>
                </div>
            </header>

            <div class="fd-body">
                <!-- Left half: Full information of the request -->
                <aside class="fd-req-panel" id="fd-req-panel">
                    <div class="fd-req-panel__head">
                        <span class="fd-req-panel__label">Request Details</span>
                        <span class="status-badge" id="fd-info-status">New</span>
                    </div>

                    <div class="fd-req-panel__content">
                        <!-- Route Card -->
                        <div class="fd-info-card">
                            <span class="fd-info-card__title">Route & Locations</span>
                            <div class="fd-route-points">
                                <div class="fd-route-point">
                                    <span class="fd-route-bullet fd-route-bullet--src"></span>
                                    <div class="fd-route-details">
                                        <span class="fd-route-city" id="fd-info-pickup-city">Pickup City</span>
                                        <span class="fd-route-address" id="fd-info-pickup-address">Pickup Address</span>
                                    </div>
                                </div>
                                <div class="fd-route-line"></div>
                                <div class="fd-route-point">
                                    <span class="fd-route-bullet fd-route-bullet--dst"></span>
                                    <div class="fd-route-details">
                                        <span class="fd-route-city" id="fd-info-dst-city">Destination City</span>
                                        <span class="fd-route-address" id="fd-info-dst-address">Destination Address</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="fd-info-card">
                            <span class="fd-info-card__title">Customer</span>
                            <div class="fd-info-grid">
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Name</span>
                                    <span class="fd-info-field__val" id="fd-info-customer">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Phone</span>
                                    <span class="fd-info-field__val" id="fd-info-phone">-</span>
                                </div>
                                <div class="fd-info-field fd-info-field--full">
                                    <span class="fd-info-field__label">Email</span>
                                    <span class="fd-info-field__val" id="fd-info-email">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cargo & Vehicle -->
                        <div class="fd-info-card">
                            <span class="fd-info-card__title">Cargo & Vehicle Specs</span>
                            <div class="fd-info-grid">
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Category</span>
                                    <span class="fd-info-field__val" id="fd-info-goods">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Load / Weight</span>
                                    <span class="fd-info-field__val" id="fd-info-load">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Vehicle Needed</span>
                                    <span class="fd-info-field__val" id="fd-info-vehicle">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Preferred Date</span>
                                    <span class="fd-info-field__val" id="fd-info-date">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Fragile</span>
                                    <span class="fd-info-field__val" id="fd-info-fragile">-</span>
                                </div>
                                <div class="fd-info-field">
                                    <span class="fd-info-field__label">Loading Help</span>
                                    <span class="fd-info-field__val" id="fd-info-loading">-</span>
                                </div>
                                <div class="fd-info-field fd-info-field--full" id="fd-info-desc-wrap">
                                    <span class="fd-info-field__label">Cargo Description</span>
                                    <span class="fd-info-field__val" id="fd-info-desc">-</span>
                                </div>
                                <div class="fd-info-field fd-info-field--full" id="fd-info-instructions-wrap">
                                    <span class="fd-info-field__label">Special Instructions</span>
                                    <span class="fd-info-field__val fd-info-field__val--note" id="fd-info-instructions">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right half: Map upside and driver list below it -->
                <div class="fd-dispatch-col">
                    <!-- Map on upper part -->
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

                    <!-- Driver list on bottom part with same width -->
                    <div class="fd-list-wrap">
                        <div class="fd-list-head">
                            <button type="button" class="fd-select-all-btn" id="fd-select-all" title="Select all" aria-label="Select all">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor" stroke-width="2"/>
                                    <path d="M7.5 12.5l3 3 6-6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <span class="fd-list-head__title">Available Drivers Nearby</span>
                        </div>
                        <ul id="fd-list" class="fd-list"></ul>
                    </div>
                </div>
            </div>

            <footer class="fd-footer">
                <span id="fd-selected" class="fd-selected">0 selected</span>
                <button type="button" class="fd-send" id="fd-send" disabled>Send Request</button>
            </footer>
        </div>
    </div>
    <div id="fd-toast" class="fd-toast" hidden>
        <span id="fd-toast-text"></span>
        <button type="button" class="fd-toast-close" data-toast-close aria-label="Close notification">×</button>
    </div>

    <style>
        /* This board needs the full workspace width, especially when the sidebar is collapsed. */
        .page-main:has(.kanban-section) {
            max-width: none;
            margin: 0;
            padding: 24px 20px var(--space-16);
        }

        /* Kanban Board layout */
        .kanban-section {
            width: 100%;
        }

        .kanban-board {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            align-items: stretch;
        }

        .kanban-column {
            background-color: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            display: flex;
            flex-direction: column;
            min-height: 560px;
            overflow: hidden;
        }

        .kanban-column__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            min-height: 54px;
            background-color: var(--color-surface-muted);
            border-bottom: 1px solid var(--color-border);
        }

        .kanban-column__title-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .kanban-column__title {
            font-size: 12px;
            font-weight: 600;
            color: var(--color-text-primary);
            line-height: 1.2;
            margin: 0;
        }

        .kanban-column__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--color-text-primary);
            background-color: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: 999px;
        }

        .kanban-column__body {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 12px;
            flex: 1;
            min-height: 200px;
        }

        .kanban-column__empty {
            display: grid;
            place-items: center;
            height: 120px;
            color: var(--color-text-muted);
            font-size: 12.5px;
            border: 1px dashed var(--color-border);
            border-radius: var(--radius-sm);
            background: rgba(255, 255, 255, 0.4);
        }

        /* Minimal clean Kanban Card - simple white */
        .kanban-card {
            background-color: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all var(--motion-fast) var(--ease-standard);
            outline: none;
            text-align: left;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .kanban-card:hover {
            background-color: var(--color-white);
            border-color: #b5c7bd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .kanban-card:focus-visible {
            border-color: var(--color-brand-primary);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--color-brand-primary) 25%, transparent);
        }

        .kanban-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .kanban-card__identity {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .kanban-card__ref {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--color-text-primary);
            letter-spacing: -0.01em;
        }

        .kanban-card__customer {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kanban-card__top .status-badge {
            font-size: 10.5px;
            height: 20px;
            padding: 0 7px;
            flex-shrink: 0;
        }

        .kanban-card__route {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 6px 8px;
            background: color-mix(in srgb, var(--color-surface-muted) 70%, transparent);
            border-radius: 4px;
        }

        .kanban-card__route-cities {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 400;
            color: var(--color-text-primary);
        }

        .kanban-card__arrow {
            color: var(--color-brand-primary);
            font-size: 12px;
        }

        .kanban-card__sub {
            font-size: 11.5px;
            color: var(--color-text-muted);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .kanban-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 4px;
            border-top: 1px solid color-mix(in srgb, var(--color-border) 40%, transparent);
            font-size: 11.5px;
            color: var(--color-text-secondary);
        }

        .kanban-card__meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 0;
            color: var(--color-text-secondary);
        }

        .kanban-card__goods-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        .kanban-card__load {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
            font-size: 11.5px;
            color: var(--color-text-muted);
        }

        .kanban-card__weight {
            font-weight: 500;
            color: var(--color-text-primary);
        }

        .kanban-card__dot {
            opacity: 0.6;
        }

        @media (max-width: 900px) {
            .kanban-board {
                grid-template-columns: repeat(2, minmax(280px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .kanban-board {
                grid-template-columns: 1fr;
            }
        }

        /* Page actions: expanding search + filters button */
        .req-actions { display: flex; align-items: center; gap: 8px; flex: none; position: relative; }
        .req-status-btn {
            border: 1px solid var(--color-brand-primary);
            border-radius: 999px;
            background: var(--color-brand-primary);
            color: #fff;
            padding: 8px 13px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        .req-status-btn:hover { background: var(--color-brand-strong); }
        .req-status-dialog .req-dialog__body { gap: 10px; }
        .accepted-driver-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            cursor: pointer;
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
            transition: all var(--motion-fast) var(--ease-standard);
        }
        .req-icon-btn:hover { color: var(--color-text-primary); border-color: #cfd8d3; }
        .req-icon-btn.is-active { color: var(--color-brand-primary); border-color: var(--color-border-brand); background: var(--color-brand-soft); }
        .req-search { display: flex; align-items: center; flex-direction: row; position: relative; }
        .req-search__field {
            position: relative;
            display: flex;
            align-items: center;
        }
        .req-search__input {
            width: 34px; height: 34px; padding: 0 34px 0 12px;
            border: 1px solid var(--color-border);
            border-radius: 999px; background: var(--color-surface);
            color: var(--color-text-primary); font-size: 13px;
            transition: width var(--motion-standard) var(--ease-standard), padding var(--motion-standard) var(--ease-standard), border-color var(--motion-fast) var(--ease-standard), box-shadow var(--motion-fast) var(--ease-standard);
        }
        .req-search__input::placeholder { color: transparent; }
        .req-search.is-open .req-search__input {
            width: 240px; padding: 0 38px 0 14px;
            border-color: var(--color-border);
        }
        .req-search.is-open .req-search__input::placeholder { color: var(--color-text-muted); }
        .req-search.is-open .req-search__input:focus { border-color: var(--color-border-brand); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-brand-primary) 16%, transparent); outline: none; }
        .req-search__btn {
            position: absolute;
            right: 2px;
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px;
            border: none; border-radius: 999px;
            background: transparent; color: var(--color-text-secondary);
            cursor: pointer;
            transition: color var(--motion-fast) var(--ease-standard), background var(--motion-fast) var(--ease-standard);
        }
        .req-search__btn:hover { color: var(--color-text-primary); background: var(--color-surface-muted); }
        .req-search__btn.is-active { color: var(--color-brand-primary); }
        .req-icon-btn:focus-visible, .req-search__btn:focus-visible { outline: 3px solid color-mix(in srgb, var(--color-brand-primary) 26%, transparent); }

        /* Filters dialog */
        .req-dialog {
            position: fixed; top: 80px; right: 24px; left: auto; bottom: auto;
            margin: 0;
            border: none; border-radius: 14px; padding: 0;
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
        .fd-overlay { position: fixed; inset: 0; background: rgba(17,27,24,.55); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .fd-overlay[hidden] { display: none; }
        .fd-modal {
            background: #fff;
            border-radius: 16px;
            width: min(1280px, 98vw);
            height: 90vh;
            max-height: 900px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 48px rgba(17,27,24,.18);
            overflow: hidden;
        }

        .fd-modal__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 22px;
            border-bottom: 1px solid var(--color-border);
            background: #ffffff;
            flex-shrink: 0;
        }

        .fd-modal__head-info {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .fd-modal__tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 3px 8px;
            background: var(--color-brand-soft);
            color: var(--color-brand-strong);
            border-radius: 4px;
        }

        .fd-modal__title {
            font-size: 17px;
            font-weight: 650;
            margin: 0;
            color: var(--color-text-primary);
        }

        .fd-modal__sub {
            display: none;
        }

        .fd-modal__head-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .fd-toolbar-inline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .fd-close {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--color-text-secondary);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .fd-close:hover {
            background: var(--color-surface-muted);
            color: var(--color-text-primary);
        }

        .fd-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--color-text-secondary);
        }

        .fd-select {
            padding: 4px 8px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 12px;
            background: #fff;
            color: var(--color-text-primary);
        }

        /* 50 / 50 layout: Left = Request details, Right = Map upside + Drivers bottom */
        .fd-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        /* Left side: Full information of the request */
        .fd-req-panel {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--color-border);
            background: #fafaf8;
            overflow-y: auto;
            scrollbar-width: none;
            min-height: 0;
        }
        .fd-req-panel::-webkit-scrollbar { display: none; }

        .fd-req-panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            background: #ffffff;
            border-bottom: 1px solid var(--color-border);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .fd-req-panel__label {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .fd-req-panel__content {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .fd-info-card {
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .fd-info-card__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted);
        }

        .fd-route-points {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            position: relative;
            gap: 14px;
        }

        .fd-route-point {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            flex: 1;
        }

        .fd-route-bullet {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .fd-route-bullet--src {
            background: var(--color-brand-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-brand-primary) 20%, transparent);
        }

        .fd-route-bullet--dst {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .fd-route-line {
            position: absolute;
            top: 9px;
            bottom: auto;
            left: calc(50% - 10px);
            width: 20px;
            height: 1px;
            background: #cbd5d1;
        }

        .fd-route-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .fd-route-city {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .fd-route-address {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin-top: 2px;
            line-height: 1.35;
        }

        .fd-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 14px;
        }

        .fd-info-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .fd-info-field--full {
            grid-column: 1 / -1;
        }

        .fd-info-field__label {
            font-size: 11px;
            color: var(--color-text-muted);
        }

        .fd-info-field__val {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--color-text-primary);
        }

        .fd-info-field__val--note {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 400;
            color: var(--color-text-secondary);
        }

        /* Right half: vertical stack (Map upside, drivers list downside with same width) */
        .fd-dispatch-col {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
            background: #ffffff;
            overflow: hidden;
        }

        .fd-map-wrap {
            position: relative;
            padding: 0;
            background: #e8eaed;
            height: 48%;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-bottom: 1px solid var(--color-border);
            flex-shrink: 0;
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
            gap: 8px;
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--color-text-primary);
            border-bottom: 1px solid var(--color-border);
            background: #fafbfc;
            flex-shrink: 0;
        }

        .fd-list-head__title {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        /* Hide Google default UI: logo, Map data, Terms, Report a map error, Shortcuts */
        .fd-map .gm-style-cc,
        .fd-map .gmnoprint a[href*='google'] { display: none !important; }
        .fd-map .gm-style .gm-soc, .fd-map .gm-ba-goto { display: none !important; }

        /* Big bold source/destination city labels */
        .fd-city-label {
            position: absolute;
            transform: translate(-50%, -190%);
            pointer-events: none;
            white-space: nowrap;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #111b18;
            text-shadow:
                0 0 3px #ffffff,
                0 0 6px #ffffff,
                0 0 9px #ffffff;
        }

        /* Floating Map Legend */
        .fd-map-legend {
            position: absolute;
            bottom: 24px;
            left: 20px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 10px;
            padding: 10px 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.14);
            font-size: 12px;
            z-index: 5;
            pointer-events: auto;
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 220px;
        }
        .fd-legend-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #5f6368;
        }
        .fd-legend-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            margin: 0;
            padding: 0;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
        }
        .fd-legend-chevron {
            font-size: 10px;
            color: #5f6368;
            transition: transform 160ms ease;
        }
        .fd-map-legend[data-collapsed] .fd-legend-chevron { transform: rotate(-90deg); }
        .fd-map-legend[data-collapsed] .fd-legend-items { display: none; }
        .fd-map-legend[data-collapsed] {
            justify-content: center;
            gap: 0;
            padding: 6px 12px;
            min-width: 0;
            border-radius: 999px;
            cursor: pointer;
        }
        .fd-map-legend[data-collapsed] .fd-legend-title { color: #202124; }
        .fd-legend-items {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .fd-legend-item {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            margin: 0;
            padding: 0;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            color: #202124;
            font-size: 12px;
            font-weight: 500;
            font-family: inherit;
        }
        .fd-legend-item.is-off { opacity: 0.4; }
        .fd-legend-casing {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 10px;
            background: #ffffff;
            border-radius: 5px;
            border: 1px solid #dcdcdc;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
            flex-shrink: 0;
            padding: 1px;
        }
        .fd-legend-line {
            display: block;
            width: 100%;
            border-radius: 3px;
        }
        .fd-legend-line--delivery {
            background: #1A73E8;
            height: 6px;
        }
        .fd-legend-line--nearest {
            background: #202124;
            height: 5px;
        }
        .fd-legend-line--other {
            background: #929AA8;
            height: 4px;
            opacity: 0.85;
        }

        /* List wrap */
        .fd-list-wrap { border-left: 1px solid var(--color-border); display: flex; flex-direction: column; min-height: 0; background: #fff; }
        .fd-list-head { display: flex; align-items: center; gap: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; color: var(--color-text-primary); border-bottom: 1px solid var(--color-border); background: #fafbfc; }
        .fd-select-all-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; flex-shrink: 0; background: none; border: none; border-radius: 8px; color: var(--color-brand-primary); cursor: pointer; }
        .fd-select-all-btn:hover { background: var(--color-brand-soft); }
        .fd-select-all-btn.is-all-selected { background: var(--color-brand-soft); }
        .fd-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
        .fd-item { display: flex; align-items: center; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--color-border); transition: background 150ms ease; cursor: pointer; }
        .fd-item:hover { background: #f8faf9; }
        .fd-item--selected { background: #f1f7f4; }
        .fd-item--selected:hover { background: #ecf3ef; }
        .fd-item--nearest { background: #fdfaf2; border-left: 3px solid #202124; }
        .fd-item--nearest:hover { background: #fbf6e8; }
        .fd-item--active { background: var(--color-brand-soft) !important; }
        .fd-item input { width: 17px; height: 17px; accent-color: var(--color-brand-primary); cursor: pointer; flex-shrink: 0; }
        .fd-item__badge { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .fd-item__badge--nearest { background: #202124; color: #fff; }
        .fd-item__badge--other { background: #e8eaed; color: #5f6368; }
        .fd-item__info { flex: 1; min-width: 0; }
        .fd-item__top { display: flex; align-items: center; gap: 6px; }
        .fd-item__name { font-size: 13.5px; font-weight: 600; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fd-item__tag { font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; letter-spacing: 0.4px; text-transform: uppercase; }
        .fd-item__tag--nearest { background: #202124; color: #fff; }
        .fd-item__meta { font-size: 12px; color: var(--color-text-secondary); margin-top: 2px; }
        .fd-item__metrics { text-align: right; flex-shrink: 0; }
        .fd-item__eta { font-size: 13px; font-weight: 700; color: #1a73e8; }
        .fd-item__dist { font-size: 11.5px; color: var(--color-text-secondary); }

        .fd-footer { display: flex; justify-content: space-between; align-items: center; padding: 14px 24px; border-top: 1px solid var(--color-border); background: #fafbfc; }
        .fd-selected { font-size: 13px; font-weight: 500; color: var(--color-text-secondary); }
        .fd-send { background: var(--color-brand-primary); color: #fff; border: none; padding: 10px 22px; border-radius: 999px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: background 150ms ease; }
        .fd-send:hover:not(:disabled) { background: var(--color-brand-strong); }
        .fd-send:disabled { opacity: .45; cursor: not-allowed; }
        
        .fd-toast { position: fixed; top: 20px; right: 20px; background: #fff; border: 1px solid var(--color-border); border-left: 4px solid var(--color-brand-primary); border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 12px 36px rgba(17,27,24,.14); z-index: 1100; max-width: 380px; }
        .fd-toast[hidden] { display: none; }
        .fd-toast-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--color-text-secondary); }

        /* Custom InfoWindow */
        .fd-info-window { font-family: var(--font-sans); padding: 4px 2px; max-width: 250px; }
        .fd-info-title { font-size: 14px; font-weight: 700; color: #202124; margin: 0 0 4px; display: flex; align-items: center; gap: 6px; }
        .fd-info-tag { font-size: 9px; font-weight: 700; padding: 2px 5px; border-radius: 4px; background: #202124; color: #fff; text-transform: uppercase; }
        .fd-info-row { font-size: 12px; color: #5f6368; margin: 2px 0; }
        .fd-info-metrics { display: flex; gap: 12px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e8eaed; }
        .fd-info-metric-val { font-size: 13px; font-weight: 700; color: #1a73e8; }
        .fd-info-metric-lbl { font-size: 10.5px; color: #70757a; }

        @media (max-width: 860px) {
            .fd-body { grid-template-columns: 1fr; }
            .fd-req-panel { border-right: none; border-bottom: 1px solid var(--color-border); max-height: 240px; }
            .fd-route-points { flex-direction: column; gap: 10px; }
            .fd-route-line { top: 14px; bottom: 18px; left: 4.5px; width: 1px; height: auto; }
            .fd-map-wrap { height: 260px; min-height: 220px; }
            .fd-list-wrap { max-height: 260px; }
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
        const mapEl = document.getElementById('fd-map');
        const list = document.getElementById('fd-list');
        const countSel = document.getElementById('fd-count');
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
                // The demo fleet has stored GPS points only around Indore. For another
                // pickup city, keep the real drivers/IDs but place the matching candidates
                // around the active source so the dispatch map reflects the pickup area.
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
                driverClusterer = new window.markerClusterer.MarkerClusterer({ map, markers: driverMarkers });
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
                        <div class="fd-item__meta">${d.vehicle} · ${d.capacity}</div>
                    </div>
                    <div class="fd-item__metrics">
                        <div class="fd-item__eta">${d.durationText}</div>
                        <div class="fd-item__dist">${d.distanceText}</div>
                    </div>
                `;

                li.addEventListener('click', (e) => {
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
                        <div class="fd-item__meta">${driver.phone || 'Phone unavailable'} · ${driver.vehicle || 'Vehicle not listed'}</div>
                    </div>`;
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

        function openModal(req) {
            currentActiveRequest = req;
            const isRecipientView = Array.isArray(req.recipientDrivers);
            const confirmMode = req.status === 'driver_accepted';
            const footer = modal.querySelector('.fd-footer');
            footer.hidden = isRecipientView && !confirmMode;
            sendBtn.textContent = confirmMode ? 'Confirm Driver' : 'Send Request';
            document.querySelector('.fd-list-head__title').textContent = 'Available Drivers Nearby';

            // Populate Request Details Panel
            fdTag.textContent = req.id || 'Request';
            if (fdInfoStatus) {
                const displayStatus = (req.status || 'new').replace(/_/g, ' ');
                fdInfoStatus.textContent = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
            }
            if (fdInfoPickupCity) fdInfoPickupCity.textContent = req.pickupCity || '-';
            if (fdInfoPickupAddress) fdInfoPickupAddress.textContent = req.pickupAddress || '-';
            if (fdInfoDstCity) fdInfoDstCity.textContent = req.destinationCity || '-';
            if (fdInfoDstAddress) fdInfoDstAddress.textContent = req.destinationAddress || '-';
            if (fdInfoCustomer) fdInfoCustomer.textContent = req.customer || '-';
            if (fdInfoPhone) fdInfoPhone.textContent = req.phone || '-';
            if (fdInfoEmail) fdInfoEmail.textContent = req.email || '-';
            if (fdInfoGoods) fdInfoGoods.textContent = req.goods || '-';
            if (fdInfoLoad) fdInfoLoad.textContent = (req.weight ? req.weight + ' kg' : '') + (req.quantity ? ' (' + req.quantity + ')' : '');
            if (fdInfoVehicle) fdInfoVehicle.textContent = req.vehicle || 'Standard Truck';
            if (fdInfoDate) fdInfoDate.textContent = req.date || 'Immediate';
            if (fdInfoFragile) fdInfoFragile.textContent = req.fragile || 'No';
            if (fdInfoLoading) fdInfoLoading.textContent = req.loading || 'Not required';

            if (req.goodsDesc) {
                fdInfoDescWrap.style.display = 'flex';
                fdInfoDesc.textContent = req.goodsDesc;
            } else {
                fdInfoDescWrap.style.display = 'none';
            }

            if (req.instructions) {
                fdInfoInstructionsWrap.style.display = 'flex';
                fdInfoInstructions.textContent = req.instructions;
            } else {
                fdInfoInstructionsWrap.style.display = 'none';
            }

            sub.textContent = req.id + ' · ' + req.pickupAddress + ' (' + req.pickupCity + ') → ' + req.destinationAddress + ' (' + req.destinationCity + ') · ' + req.goods + ' · ' + req.weight + ' kg';
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
                    recipientDrivers: row.dataset.status === 'driver_accepted'
                        ? JSON.parse(row.dataset.acceptedDrivers || '[]')
                        : (['invitations_sent', 'awaiting_responses'].includes(row.dataset.status)
                            ? JSON.parse(row.dataset.sentDrivers || '[]')
                            : (['confirmed', 'closed'].includes(row.dataset.status)
                                ? JSON.parse(row.dataset.confirmedDrivers || '[]')
                                : undefined)),
                    recipientTitle: row.dataset.status === 'driver_accepted'
                        ? 'Drivers who accepted'
                        : (['confirmed', 'closed'].includes(row.dataset.status) ? 'Confirmed driver' : 'Drivers notified'),
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
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

        countSel.addEventListener('change', () => {
            const candidates = getCandidateDrivers(SRC);
            renderMapAndRoutes(candidates);
        });

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
            selectedEl.textContent = selected.size + ' selected';
            sendBtn.disabled = selected.size === 0;
        }

        function moveCardToSentColumn(cardEl, notifiedDrivers = []) {
            if (!cardEl) return;
            const targetCol = document.getElementById('kanban-col-sent');
            if (!targetCol) return;
            const targetBody = targetCol.querySelector('.kanban-column__body');
            const targetCount = targetCol.querySelector('.kanban-column__count');
            const sourceCol = cardEl.closest('.kanban-column');
            const sourceCount = sourceCol ? sourceCol.querySelector('.kanban-column__count') : null;

            // Remove any "No requests" placeholder in target column
            const targetEmpty = targetBody.querySelector('.kanban-column__empty');
            if (targetEmpty) targetEmpty.remove();

            // Update badge inside card if present
            const badge = cardEl.querySelector('.status-badge');
            if (badge) {
                badge.className = 'status-badge status-badge--attention';
                badge.textContent = 'Awaiting responses';
            }
            cardEl.dataset.status = 'awaiting_responses';
            cardEl.dataset.sentDrivers = JSON.stringify(notifiedDrivers);

            // Move the card to top of the target column
            targetBody.prepend(cardEl);

            // Update column counters
            if (sourceCol && sourceCount && sourceCol !== targetCol) {
                const sCards = sourceCol.querySelectorAll('.kanban-card').length;
                sourceCount.textContent = sCards;
                if (sCards === 0) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'kanban-column__empty';
                    emptyDiv.innerHTML = '<span>No requests</span>';
                    sourceCol.querySelector('.kanban-column__body').appendChild(emptyDiv);
                }
            }

            if (targetCount) {
                targetCount.textContent = targetBody.querySelectorAll('.kanban-card').length;
            }
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
                    closeModal();
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
                    moveCardToSentColumn(activeReq.cardEl);
                }
                sendBtn.textContent = prevText;
                closeModal();
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
        const searchField = searchForm.querySelector('.req-search__field');
        const filtersBtn = document.getElementById('req-filters-btn');
        const filtersDialog = document.getElementById('req-filters-dialog');
        const statusBtn = document.getElementById('req-status-btn');
        const statusDialog = document.getElementById('req-status-dialog');
        const statusForm = document.getElementById('req-status-form');
        const statusRequest = document.getElementById('status-request');
        const statusValue = document.getElementById('status-value');
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

        filtersBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openDialog(filtersDialog, filtersBtn);
        });

        statusBtn.addEventListener('click', () => openDialog(statusDialog, statusBtn));

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

        const searchSubmitBtn = searchForm.querySelector('.req-search__btn');
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

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && searchForm.classList.contains('is-open')) {
                if (searchInput.value.trim() !== '') {
                    searchForm.submit();
                }
            }
        });

        filtersDialog.querySelectorAll('[data-dialog-close]').forEach(b => b.addEventListener('click', () => filtersDialog.close()));

        filtersDialog.addEventListener('click', e => {
            if (e.target === filtersDialog) filtersDialog.close();
        });

        statusDialog.querySelectorAll('[data-status-dialog-close]').forEach(button => {
            button.addEventListener('click', () => statusDialog.close());
        });

        statusDialog.addEventListener('click', e => {
            if (e.target === statusDialog) statusDialog.close();
        });

        statusForm.addEventListener('submit', async e => {
            e.preventDefault();
            const submit = statusForm.querySelector('[type="submit"]');
            submit.disabled = true;

            try {
                const response = await fetch(`/requests/${statusRequest.value}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ status: statusValue.value }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Unable to update the request status.');

                window.location.reload();
            } catch (error) {
                window.alert(error.message || 'Unable to update the request status.');
            } finally {
                submit.disabled = false;
            }
        });

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

        const columnStatuses = {
            new: 'new',
            sent: 'awaiting_responses',
            in_progress: 'driver_accepted',
            confirmed: 'confirmed',
            journey_completed: 'closed',
        };

        document.querySelectorAll('.kanban-card[draggable="true"]').forEach(card => {
            card.addEventListener('dragstart', event => {
                event.dataTransfer.setData('text/plain', card.dataset.dbId);
                event.dataTransfer.effectAllowed = 'move';
            });
        });

        document.querySelectorAll('.kanban-column').forEach(column => {
            column.addEventListener('dragover', event => event.preventDefault());
            column.addEventListener('drop', async event => {
                event.preventDefault();
                const requestId = event.dataTransfer.getData('text/plain');
                const status = columnStatuses[column.dataset.column];
                if (!requestId || !status) return;
                const response = await fetch(`/requests/${requestId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ status }),
                });
                if (response.ok) window.location.reload();
            });
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (filtersDialog.open) {
                    filtersDialog.close();
                    return;
                }
                if (statusDialog.open) {
                    statusDialog.close();
                    return;
                }
                if (searchForm.classList.contains('is-open') && searchInput.value === '') {
                    searchForm.classList.remove('is-open');
                    searchInput.blur();
                }
            }
        });

        document.addEventListener('click', (e) => {
            if (!searchForm.contains(e.target) && searchInput.value === '') {
                searchForm.classList.remove('is-open');
            }
        });

        searchBtnExpansion();
        function searchBtnExpansion() {
            searchInput.addEventListener('focus', () => searchForm.classList.add('is-open'));
            searchInput.addEventListener('blur', () => {
                setTimeout(() => {
                    if (document.activeElement === searchSubmitBtn || searchForm.contains(document.activeElement)) {
                        return;
                    }
                    if (searchInput.value === '') {
                        searchForm.classList.remove('is-open');
                    }
                }, 150);
            });
            if (searchInput.value !== '') searchForm.classList.add('is-open');
        }
    })();
    </script>
@endsection
