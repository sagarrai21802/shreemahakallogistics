@extends('layouts.app')

@section('title', 'My Account')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">My Account</h1>
            <p class="page-meta">Your sign-in details and password.</p>
        </div>
    </header>

    <div class="settings-grid">
        <section class="panel" aria-labelledby="profile-heading">
            <header class="panel__head">
                <div>
                    <h2 class="panel__title" id="profile-heading">Profile</h2>
                    <p class="panel__meta">Vendor account</p>
                </div>
            </header>

            <div class="panel__body">
                <dl class="detail-list">
                    <div class="detail-row">
                        <dt class="detail-label">Name</dt>
                        <dd class="detail-value">{{ $user->name }}</dd>
                    </div>
                    <div class="detail-row">
                        <dt class="detail-label">Username</dt>
                        <dd class="detail-value">{{ $user->username }}</dd>
                    </div>
                    <div class="detail-row">
                        <dt class="detail-label">Email</dt>
                        <dd class="detail-value">{{ $user->email }}</dd>
                    </div>
                    <div class="detail-row">
                        <dt class="detail-label">Member since</dt>
                        <dd class="detail-value">{{ $user->created_at->format('j M Y') }}</dd>
                    </div>
                </dl>

                <p class="panel__note">Sign-in details are managed by the account owner.</p>
            </div>
        </section>

        <section class="panel" aria-labelledby="password-heading">
            <header class="panel__head">
                <div>
                    <h2 class="panel__title" id="password-heading">Password</h2>
                    <p class="panel__meta">Use at least 8 characters</p>
                </div>
            </header>

            <div class="panel__body">
                <form class="form form--flush" method="POST" action="{{ route('account.password') }}">
                    @csrf

                    <div class="field">
                        <label class="field__label" for="current_password">Current password</label>
                        <input
                            class="field__input"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            @error('current_password') aria-invalid="true" aria-describedby="current_password-error" @enderror
                        >
                        @error('current_password')
                            <p class="field__error" id="current_password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">New password</label>
                        <input
                            class="field__input"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        >
                        @error('password')
                            <p class="field__error" id="password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label" for="password_confirmation">Confirm new password</label>
                        <input
                            class="field__input"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                        >
                    </div>

                    <div>
                        <button class="btn btn--primary btn--fit" type="submit">Update password</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
