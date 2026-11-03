@extends('layouts.app')

@section('title', 'Drivers')

@section('content')
    @php
        $availabilityLabels = [
            'available' => 'Available',
            'busy' => 'Busy',
            'offline' => 'Offline',
            'location_stale' => 'Location stale',
        ];

        $availabilityTones = [
            'available' => 'success',
            'busy' => 'neutral',
            'offline' => 'neutral',
            'location_stale' => 'attention',
        ];
    @endphp

    <header class="page-head">
        <div>
            <h1 class="page-title">Drivers</h1>
            <p class="page-meta">Approved drivers and availability for matching.</p>
        </div>
        <span class="page-count">{{ $drivers->total() }} {{ Str::plural('driver', $drivers->total()) }}</span>
    </header>

    <form class="filters" method="GET" action="{{ route('drivers.index') }}" role="search">
        <div class="filter filter--grow">
            <label class="filter__label" for="driver-search">Search</label>
            <input
                class="filter__input"
                id="driver-search"
                type="search"
                name="q"
                value="{{ $q }}"
                placeholder="Name, reference, phone or vehicle"
                autocomplete="off"
            >
        </div>

        <div class="filter">
            <label class="filter__label" for="driver-approval">Approval</label>
            <select class="filter__select" id="driver-approval" name="approval">
                <option value="">All approvals</option>
                @foreach ($approvalStatuses as $option)
                    <option value="{{ $option }}" @selected($approval === $option)>{{ Str::headline($option) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter">
            <label class="filter__label" for="driver-availability">Availability</label>
            <select class="filter__select" id="driver-availability" name="availability">
                <option value="">All availability</option>
                @foreach ($availabilityStatuses as $option)
                    <option value="{{ $option }}" @selected($availability === $option)>{{ $availabilityLabels[$option] }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter__actions">
            <button class="btn btn--quiet" type="submit">Apply filters</button>
            @if ($q !== '' || $approval !== '' || $availability !== '')
                <a class="btn btn--quiet" href="{{ route('drivers.index') }}">Clear</a>
            @endif
        </div>
    </form>

    <section class="panel" aria-label="Drivers">
        @if ($drivers->isEmpty())
            <div class="empty-state">
                <p class="empty-state__title">No drivers found</p>
                <p class="empty-state__text">
                    @if ($q !== '' || $approval !== '' || $availability !== '')
                        No drivers match these filters.
                        <a class="text-link" href="{{ route('drivers.index') }}">Clear filters</a>
                    @else
                        Approved drivers will appear here.
                    @endif
                </p>
            </div>
        @else
            <div class="table-scroll">
                <table class="data-table data-table--drivers">
                    <thead>
                        <tr>
                            <th scope="col">Driver</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Vehicle</th>
                            <th scope="col">Capacity</th>
                            <th scope="col">Approval</th>
                            <th scope="col">Availability</th>
                            <th scope="col">Last location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            <tr>
                                <td>
                                    <span class="cell-ref">{{ $driver->name }}</span>
                                    <span class="cell-sub">{{ $driver->partner_ref }}</span>
                                </td>
                                <td class="cell-nowrap">
                                    <span class="cell-title">{{ $driver->phone }}</span>
                                    @if ($driver->email)
                                        <span class="cell-sub">{{ $driver->email }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="cell-title">{{ $driver->vehicle_type }}</span>
                                    <span class="cell-sub">{{ $driver->vehicle_model }} · {{ $driver->registration_number }}</span>
                                </td>
                                <td class="cell-nowrap">{{ rtrim(rtrim(number_format((float) $driver->capacity_tons, 2), '0'), '.') }} t</td>
                                <td><x-status-badge :status="$driver->approval_status" /></td>
                                <td class="cell-nowrap">
                                    <span class="availability-state">
                                        <span class="dot dot--{{ $availabilityTones[$driver->availability_status] ?? 'neutral' }}" aria-hidden="true"></span>
                                        {{ $availabilityLabels[$driver->availability_status] ?? Str::headline($driver->availability_status) }}
                                    </span>
                                </td>
                                <td class="cell-nowrap">
                                    @if ($driver->last_location_at)
                                        <time datetime="{{ $driver->last_location_at->toIso8601String() }}" title="{{ $driver->last_location_at->format('D, j M Y, H:i') }}">
                                            {{ $driver->last_location_at->diffForHumans() }}
                                        </time>
                                    @else
                                        <span class="cell-muted">Not shared</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$drivers" />
        @endif
    </section>
@endsection
