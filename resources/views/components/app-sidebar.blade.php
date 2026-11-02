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
        ['label' => 'Overview', 'icon' => 'overview', 'href' => route('dashboard'), 'available' => true, 'active' => request()->routeIs('dashboard')],
        ['label' => 'Transport Requests', 'icon' => 'requests', 'available' => false],
        ['label' => 'Drivers', 'icon' => 'drivers', 'available' => false],
        ['label' => 'Driver Applications', 'icon' => 'applications', 'available' => false],
        ['label' => 'Activity', 'icon' => 'activity', 'available' => false],
    ];

    $utilityItems = [
        ['label' => 'Help & Support', 'icon' => 'help', 'available' => false],
        ['label' => 'My Account', 'icon' => 'account', 'available' => false],
    ];
@endphp

<aside id="app-sidebar" class="sidebar" aria-label="Workspace">
    <div class="sidebar__head">
        <a class="sidebar__brand" href="{{ route('dashboard') }}" aria-label="Shreemahakal Logistics, go to Overview">
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
                    @if ($item['available'] ?? false)
                        <a
                            class="nav-item {{ ($item['active'] ?? false) ? 'is-active' : '' }}"
                            href="{{ $item['href'] }}"
                            title="{{ $item['label'] }}"
                            @if ($item['active'] ?? false) aria-current="page" @endif
                        >
                            <x-icon :name="$item['icon']" class="nav-item__icon" />
                            <span class="nav-item__label">{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span class="nav-item is-disabled" aria-disabled="true" title="{{ $item['label'] }} — not available yet">
                            <x-icon :name="$item['icon']" class="nav-item__icon" />
                            <span class="nav-item__label">{{ $item['label'] }}</span>
                            <span class="nav-item__soon">Soon</span>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="sidebar__foot">
        <nav class="sidebar__utility" aria-label="Account and support">
            <ul class="sidebar__list">
                @foreach ($utilityItems as $item)
                    <li>
                        <span class="nav-item nav-item--utility is-disabled" aria-disabled="true" title="{{ $item['label'] }} — not available yet">
                            <x-icon :name="$item['icon']" class="nav-item__icon" />
                            <span class="nav-item__label">{{ $item['label'] }}</span>
                            <span class="nav-item__soon">Soon</span>
                        </span>
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
