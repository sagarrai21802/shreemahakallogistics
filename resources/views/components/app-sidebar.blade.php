@php
    $user = auth()->user();
    $userName = $user?->name ?? 'Vendor';
    $userNameHandle = $user?->username ? '@'.$user->username : null;

    $initials = collect(preg_split('/\s+/', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    $primaryItems = [
        // ['label' => 'Overview', 'icon' => 'overview', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Transport Requests', 'icon' => 'requests', 'href' => route('requests.index'), 'active' => request()->routeIs('requests.*')],
        ['label' => 'Drivers', 'icon' => 'drivers', 'href' => route('drivers.index'), 'active' => request()->routeIs('drivers.*')],
        ['label' => 'Driver Applications', 'icon' => 'applications', 'href' => route('applications.index'), 'active' => request()->routeIs('applications.*')],
        ['label' => 'Activity', 'icon' => 'activity', 'href' => route('activity.index'), 'active' => request()->routeIs('activity.*')],
    ];

    $utilityItems = [
        ['label' => 'Help & Support', 'icon' => 'help', 'href' => route('help'), 'active' => request()->routeIs('help')],
        ['label' => 'My Account', 'icon' => 'account', 'href' => route('account'), 'active' => request()->routeIs('account')],
    ];
@endphp

<aside id="app-sidebar" class="sidebar" aria-label="Workspace">
    <div class="sidebar__head">
        <a class="sidebar__brand" href="{{ route('requests.index') }}" aria-label="Shreemahakal Logistics, go to Transport Requests">
            <span class="sidebar__brand-mark" aria-hidden="true">SM</span>
            <span class="sidebar__brand-text">
                <span class="sidebar__brand-name">Shreemahakal</span>
                <span class="sidebar__brand-sub">Logistics</span>
            </span>
        </a>

        <button
            type="button"
            class="icon-btn icon-btn--sm sidebar__collapse"
            data-sidebar-toggle
            aria-controls="app-sidebar"
            aria-expanded="true"
            aria-label="Collapse sidebar"
        >
            <x-icon name="collapse" />
        </button>
    </div>

    <nav class="sidebar__nav" aria-label="Primary">
        <ul class="sidebar__list">
            @foreach ($primaryItems as $item)
                <li>
                    <a
                        class="nav-item {{ $item['active'] ? 'is-active' : '' }}"
                        href="{{ $item['href'] }}"
                        title="{{ $item['label'] }}"
                        @if ($item['active']) aria-current="page" @endif
                    >
                        <x-icon :name="$item['icon']" class="nav-item__icon" />
                        <span class="nav-item__label">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="sidebar__foot">
        <nav class="sidebar__utility" aria-label="Account and support">
            <ul class="sidebar__list">
                @foreach ($utilityItems as $item)
                    <li>
                        <a
                            class="nav-item nav-item--utility {{ $item['active'] ? 'is-active' : '' }}"
                            href="{{ $item['href'] }}"
                            title="{{ $item['label'] }}"
                            @if ($item['active']) aria-current="page" @endif
                        >
                            <x-icon :name="$item['icon']" class="nav-item__icon" />
                            <span class="nav-item__label">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="sidebar__account" title="{{ $userName }}{{ $userNameHandle ? ' '.$userNameHandle : '' }}">
            <span class="sidebar__avatar" aria-hidden="true">{{ $initials ?: 'SM' }}</span>
            <span class="sidebar__identity">
                <span class="sidebar__name">{{ $userName }}</span>
                @if ($userNameHandle)
                    <span class="sidebar__handle">{{ $userNameHandle }}</span>
                @endif
            </span>

            <form class="sidebar__logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn" aria-label="Sign out" title="Sign out">
                    <x-icon name="logout" />
                </button>
            </form>
        </div>
    </div>
</aside>
