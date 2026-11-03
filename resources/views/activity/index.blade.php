@extends('layouts.app')

@section('title', 'Activity')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Activity</h1>
            <p class="page-meta">Requests, invitations and driver responses in one timeline.</p>
        </div>
        <span class="page-count">{{ $events->total() }} {{ Str::plural('event', $events->total()) }}</span>
    </header>

    <form class="filters" method="GET" action="{{ route('activity.index') }}">
        <div class="filter">
            <label class="filter__label" for="activity-type">Event type</label>
            <select class="filter__select" id="activity-type" name="type">
                <option value="">All events</option>
                @foreach ($types as $option)
                    <option value="{{ $option }}" @selected($type === $option)>{{ Str::headline($option) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter__actions">
            <button class="btn btn--quiet" type="submit">Apply filter</button>
            @if ($type !== '')
                <a class="btn btn--quiet" href="{{ route('activity.index') }}">Clear</a>
            @endif
        </div>
    </form>

    <section class="panel" aria-label="Activity timeline">
        @if ($events->isEmpty())
            <div class="empty-state">
                <p class="empty-state__title">No activity found</p>
                <p class="empty-state__text">
                    @if ($type !== '')
                        No events match this filter.
                        <a class="text-link" href="{{ route('activity.index') }}">Clear filter</a>
                    @else
                        Operational events will appear here.
                    @endif
                </p>
            </div>
        @else
            <ol class="activity">
                @foreach ($events as $event)
                    <li class="activity__item">
                        <span class="activity__dot activity__dot--{{ $event->tone() }}" aria-hidden="true"></span>
                        <div class="activity__body">
                            <p class="activity__title">{{ $event->title }}</p>
                            @if ($event->description)
                                <p class="activity__desc">{{ $event->description }}</p>
                            @endif
                        </div>
                        <time class="activity__time" datetime="{{ $event->occurred_at->toIso8601String() }}" title="{{ $event->occurred_at->format('D, j M Y, H:i') }}">
                            {{ $event->occurred_at->diffForHumans() }}
                        </time>
                    </li>
                @endforeach
            </ol>

            <x-pagination :paginator="$events" />
        @endif
    </section>
@endsection
