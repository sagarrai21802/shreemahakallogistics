<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="expanded">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Shreemahakal Logistics</title>
    <script>
        try {
            if (window.localStorage.getItem('sidebar-collapsed') === '1') {
                document.documentElement.dataset.sidebar = 'collapsed';
            }
        } catch {
            // Keep the expanded default when storage is unavailable.
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="shell-page">
    <div class="shell">
        <x-app-sidebar />

        <div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true"></div>

        <div class="shell__main">
            <button
                type="button"
                class="shell__menu"
                data-drawer-toggle
                aria-controls="app-sidebar"
                aria-expanded="false"
                aria-label="Open navigation"
            >
                <x-icon name="menu" />
            </button>

            <main class="page-main">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
