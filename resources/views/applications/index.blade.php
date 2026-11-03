@extends('layouts.app')

@section('title', 'Driver Applications')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Driver Applications</h1>
            <p class="page-meta">New driver submissions and document checks.</p>
        </div>
        <span class="page-count">{{ $applications->total() }} {{ Str::plural('application', $applications->total()) }}</span>
    </header>

    <form class="filters" method="GET" action="{{ route('applications.index') }}">
        <div class="filter">
            <label class="filter__label" for="application-status">Status</label>
            <select class="filter__select" id="application-status" name="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter__actions">
            <button class="btn btn--quiet" type="submit">Apply filter</button>
            @if ($status !== '')
                <a class="btn btn--quiet" href="{{ route('applications.index') }}">Clear</a>
            @endif
        </div>
    </form>

    <section class="panel" aria-label="Driver applications">
        @if ($applications->isEmpty())
            <div class="empty-state">
                <p class="empty-state__title">No applications found</p>
                <p class="empty-state__text">
                    @if ($status !== '')
                        No applications match this filter.
                        <a class="text-link" href="{{ route('applications.index') }}">Clear filter</a>
                    @else
                        New driver applications will appear here.
                    @endif
                </p>
            </div>
        @else
            <div class="table-scroll">
                <table class="data-table data-table--applications">
                    <thead>
                        <tr>
                            <th scope="col">Applicant</th>
                            <th scope="col">Vehicle</th>
                            <th scope="col">Documents</th>
                            <th scope="col">Submitted</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>
                                    <span class="cell-ref">{{ $application->driver_name }}</span>
                                    <span class="cell-sub">{{ $application->partner_ref }}</span>
                                </td>
                                <td>{{ $application->vehicle_type }}</td>
                                <td class="cell-nowrap">
                                    {{ $application->documents_received }} of {{ $application->documents_total }}
                                    @if ($application->documents_received < $application->documents_total)
                                        <span class="cell-sub">{{ $application->documents_total - $application->documents_received }} pending</span>
                                    @endif
                                </td>
                                <td class="cell-nowrap">
                                    <time datetime="{{ $application->submitted_at->toIso8601String() }}" title="{{ $application->submitted_at->format('D, j M Y, H:i') }}">
                                        {{ $application->submitted_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td><x-status-badge :status="$application->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$applications" />
        @endif
    </section>
@endsection
