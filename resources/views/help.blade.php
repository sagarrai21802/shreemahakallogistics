@extends('layouts.app')

@section('title', 'Help & Support')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Help &amp; Support</h1>
            <p class="page-meta">Get help with requests, drivers and your account.</p>
        </div>
    </header>

    <div class="settings-grid">
        <section class="panel" aria-labelledby="contact-heading">
            <header class="panel__head">
                <div>
                    <h2 class="panel__title" id="contact-heading">Contact support</h2>
                    <p class="panel__meta">Operations team</p>
                </div>
            </header>

            <div class="panel__body">
                <p class="body-text">Email us when a request, driver or account needs attention.</p>
                <p class="body-text">
                    <a class="text-link" href="mailto:{{ config('portal.support_email') }}">{{ config('portal.support_email') }}</a>
                </p>
                <p class="panel__note">Include the request reference, driver reference or the page you are working on so we can find it quickly.</p>
            </div>
        </section>

        <section class="panel" aria-labelledby="links-heading">
            <header class="panel__head">
                <div>
                    <h2 class="panel__title" id="links-heading">Common tasks</h2>
                    <p class="panel__meta">Where to find things</p>
                </div>
            </header>

            <div class="panel__body">
                <ul class="plain-list">
                    <li class="plain-list__item">
                        <a class="text-link" href="{{ route('requests.index') }}">Review transport requests</a>
                        <span class="plain-list__meta">Customer requests and driver responses.</span>
                    </li>
                    <li class="plain-list__item">
                        <a class="text-link" href="{{ route('drivers.index') }}">Check driver availability</a>
                        <span class="plain-list__meta">Approved drivers and their last location.</span>
                    </li>
                    <li class="plain-list__item">
                        <a class="text-link" href="{{ route('applications.index') }}">Review driver applications</a>
                        <span class="plain-list__meta">New submissions and document checks.</span>
                    </li>
                    <li class="plain-list__item">
                        <a class="text-link" href="{{ route('activity.index') }}">Trace recent activity</a>
                        <span class="plain-list__meta">Requests, invitations and responses.</span>
                    </li>
                </ul>
            </div>
        </section>
    </div>
@endsection
