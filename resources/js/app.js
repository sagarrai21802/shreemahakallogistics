const root = document.documentElement;
const sidebar = document.getElementById('app-sidebar');
const collapseButton = document.querySelector('[data-sidebar-toggle]');
const drawerButton = document.querySelector('[data-drawer-toggle]');
const overlay = document.querySelector('[data-sidebar-overlay]');
const mobileQuery = window.matchMedia('(max-width: 1023px)');

const isMobile = () => mobileQuery.matches;

function isCollapsed() {
    return root.dataset.sidebar === 'collapsed';
}

function isDrawerOpen() {
    return root.dataset.drawer === 'open';
}

function syncToggleLabel() {
    if (!collapseButton) {
        return;
    }

    const label = isMobile()
        ? 'Close navigation'
        : (isCollapsed() ? 'Expand sidebar' : 'Collapse sidebar');

    collapseButton.setAttribute('aria-label', label);
    collapseButton.setAttribute('aria-expanded', String(isMobile() ? isDrawerOpen() : !isCollapsed()));
}

function syncDrawerAvailability() {
    if (!sidebar) {
        return;
    }

    sidebar.inert = isMobile() && !isDrawerOpen();
}

function setCollapsed(collapsed) {
    root.dataset.sidebar = collapsed ? 'collapsed' : 'expanded';

    try {
        window.localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0');
    } catch {
        // Storage may be unavailable; keep the in-memory state.
    }

    syncToggleLabel();
}

function openDrawer() {
    root.dataset.drawer = 'open';
    drawerButton?.setAttribute('aria-expanded', 'true');
    syncDrawerAvailability();
    syncToggleLabel();

    sidebar?.querySelector('a, button')?.focus();
}

function closeDrawer({ restoreFocus = false } = {}) {
    if (!isDrawerOpen()) {
        return;
    }

    delete root.dataset.drawer;
    drawerButton?.setAttribute('aria-expanded', 'false');
    syncDrawerAvailability();
    syncToggleLabel();

    if (restoreFocus) {
        drawerButton?.focus();
    }
}

collapseButton?.addEventListener('click', () => {
    if (isMobile()) {
        closeDrawer({ restoreFocus: true });

        return;
    }

    setCollapsed(!isCollapsed());
});

drawerButton?.addEventListener('click', () => {
    if (isDrawerOpen()) {
        closeDrawer({ restoreFocus: true });
    } else {
        openDrawer();
    }
});

overlay?.addEventListener('click', () => closeDrawer());

sidebar?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        if (isMobile()) {
            closeDrawer();
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isDrawerOpen()) {
        closeDrawer({ restoreFocus: true });
    }
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (! window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

mobileQuery.addEventListener('change', () => {
    if (!isMobile()) {
        closeDrawer();
    }

    syncDrawerAvailability();
    syncToggleLabel();
});

syncDrawerAvailability();
syncToggleLabel();
