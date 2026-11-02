<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') · Shreemahakal Logistics</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth">
    <aside class="auth__brand">
        <span class="brand">
            <span class="brand__name">Shreemahakal</span>
            <span class="brand__suffix">Logistics</span>
        </span>

        <p class="auth__statement">Operations, without the noise.</p>
    </aside>

    <main class="auth__panel">
        <div class="auth__content">
            @yield('content')
        </div>
    </main>
</body>
</html>
