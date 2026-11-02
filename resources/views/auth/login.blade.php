@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <h1 class="auth__title">Sign in</h1>
    <p class="auth__lede">Enter your credentials to continue.</p>

    <form class="form" method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="field">
            <label class="field__label" for="username">Username</label>
            <input
                class="field__input"
                id="username"
                name="username"
                type="text"
                value="{{ old('username') }}"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                required
                autofocus
                @error('username') aria-invalid="true" aria-describedby="username-error" @enderror
            >
            @error('username')
                <p class="field__error" id="username-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label class="field__label" for="password">Password</label>
            <input
                class="field__input"
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
            >
            @error('password')
                <p class="field__error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn btn--primary" type="submit">Sign in</button>
    </form>
@endsection
