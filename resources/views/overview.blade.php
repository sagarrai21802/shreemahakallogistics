@extends('layouts.app')

@section('title', 'Overview')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Overview</h1>
            <p class="page-meta">{{ now()->format('l, j F Y') }}</p>
        </div>
        <form method="POST" action="{{ route('demo.reset') }}" data-confirm="Reset all demo data? This replaces the current requests, drivers, applications and activity.">
            @csrf
            <button type="submit" class="btn btn--quiet">Reset</button>
        </form>
    </header>

    <section class="kpi-grid" aria-label="Operations summary">
        <article class="kpi">
            <p class="kpi__label">New requests</p>
            <p class="kpi__value">{{ $stats['new_requests'] }}</p>
            <p class="kpi__meta">
                @if ($oldestNewRequest)
                    Oldest waiting {{ $oldestNewRequest->submitted_at->diffForHumans() }}
                @else
                    Nothing waiting for review
                @endif
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">Awaiting responses</p>
            <p class="kpi__value">{{ $stats['awaiting_responses'] }}</p>
            <p class="kpi__meta">Requests with pending invitations</p>
        </article>

        <article class="kpi">
            <p class="kpi__label">Follow-up needed</p>
            <p class="kpi__value">{{ $stats['follow_up'] }}</p>
            <p class="kpi__meta">Accepted drivers waiting to be called</p>
        </article>

        <article class="kpi">
            <p class="kpi__label">Available drivers now</p>
            <p class="kpi__value">{{ $stats['available_drivers'] }}</p>
            <p class="kpi__meta">Location updated in the last {{ $freshnessMinutes }} minutes</p>
        </article>
    </section>

    <section class="panel overview__attention" aria-labelledby="attention-heading">
        <header class="panel__head">
            <div>
                <h2 class="panel__title" id="attention-heading">Requests needing attention</h2>
                <p class="panel__meta">Newest first</p>
            </div>
            <span class="panel__count">{{ $attention->count() }}</span>
        </header>

        @if ($attention->isEmpty())
            <div class="empty-state">
                <p class="empty-state__title">No requests need attention</p>
                <p class="empty-state__text">New customer requests will appear here as they arrive.</p>
            </div>
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Request</th>
                            <th scope="col">Route</th>
                            <th scope="col">Goods</th>
                            <th scope="col">Received</th>
                            <th scope="col">Drivers</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="sr-only">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attention as $request)
                            <tr>
                                <td>
                                    <span class="cell-ref">{{ $request->reference }}</span>
                                    <span class="cell-sub">{{ $request->quantity }} · {{ number_format($request->weight_kg) }} kg</span>
                                </td>
                                <td>
                                    <span class="route">
                                        <span>{{ $request->pickup_city }}</span>
                                        <span class="route__arrow" aria-hidden="true">→</span>
                                        <span>{{ $request->destination_city }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="cell-title">{{ $request->goods_category }}</span>
                                    <span class="cell-sub cell-sub--truncate" title="{{ $request->goods_description }}">{{ $request->goods_description }}</span>
                                </td>
                                <td>
                                    <time datetime="{{ $request->submitted_at->toIso8601String() }}" title="{{ $request->submitted_at->format('D, j M Y, H:i') }}">
                                        {{ $request->submitted_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td class="cell-nowrap">
                                    @if ($request->invited_count > 0)
                                        {{ $request->accepted_count }} of {{ $request->invited_count }} accepted
                                    @else
                                        <span class="cell-muted">Not sent</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$request->status" /></td>
                                <td class="cell-action">
                                    <a class="row-action" href="{{ route('requests.index', ['q' => $request->reference]) }}" title="Open in Transport Requests">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="overview__lower">
        <section class="panel" aria-labelledby="activity-heading">
            <header class="panel__head">
                <div>
                    <h2 class="panel__title" id="activity-heading">Recent activity</h2>
                    <p class="panel__meta">Latest operational events</p>
                </div>
            </header>

            @if ($activity->isEmpty())
                <div class="empty-state empty-state--compact">
                    <p class="empty-state__title">No activity yet</p>
                </div>
            @else
                <ol class="activity">
                    @foreach ($activity as $event)
                        <li class="activity__item">
                            <span class="activity__dot activity__dot--{{ $event->tone() }}" aria-hidden="true"></span>
                            <div class="activity__body">
                                <p class="activity__title">{{ $event->title }}</p>
                                @if ($event->description)
                                    <p class="activity__desc">{{ $event->description }}</p>
                                @endif
                            </div>
                            <time class="activity__time" datetime="{{ $event->occurred_at->toIso8601String() }}">
                                {{ $event->occurred_at->diffForHumans() }}
                            </time>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <aside class="overview__rail" aria-label="Operational snapshot">
            <section class="panel" aria-labelledby="availability-heading">
                <header class="panel__head">
                    <div>
                        <h2 class="panel__title" id="availability-heading">Driver availability</h2>
                        <p class="panel__meta">Approved drivers</p>
                    </div>
                </header>

                <div class="panel__body">
                    <ul class="availability">
                        <li class="availability__row">
                            <span class="availability__label"><span class="dot dot--success" aria-hidden="true"></span>Available</span>
                            <span class="availability__value">{{ $availability['available'] ?? 0 }}</span>
                        </li>
                        <li class="availability__row">
                            <span class="availability__label"><span class="dot dot--neutral" aria-hidden="true"></span>Busy</span>
                            <span class="availability__value">{{ $availability['busy'] ?? 0 }}</span>
                        </li>
                        <li class="availability__row">
                            <span class="availability__label"><span class="dot dot--neutral" aria-hidden="true"></span>Offline</span>
                            <span class="availability__value">{{ $availability['offline'] ?? 0 }}</span>
                        </li>
                        <li class="availability__row">
                            <span class="availability__label"><span class="dot dot--attention" aria-hidden="true"></span>Location stale</span>
                            <span class="availability__value">{{ $availability['location_stale'] ?? 0 }}</span>
                        </li>
                    </ul>

                    <p class="panel__note">
                        Available means the driver is marked available and shared a location within the last {{ $freshnessMinutes }} minutes.
                    </p>
                </div>
            </section>

            <section class="panel" aria-labelledby="applications-heading">
                <header class="panel__head">
                    <div>
                        <h2 class="panel__title" id="applications-heading">Driver applications</h2>
                        <p class="panel__meta">{{ $stats['pending_applications'] }} pending review</p>
                    </div>
                </header>

                @if ($applications->isEmpty())
                    <div class="empty-state empty-state--compact">
                        <p class="empty-state__title">No applications need review</p>
                    </div>
                @else
                    <ul class="mini-list">
                        @foreach ($applications as $application)
                            <li class="mini-item">
                                <div class="mini-item__body">
                                    <p class="mini-item__title">{{ $application->driver_name }}</p>
                                    <p class="mini-item__meta">{{ $application->partner_ref }} · {{ $application->vehicle_type }}</p>
                                </div>
                                <x-status-badge :status="$application->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </div>
@endsection
