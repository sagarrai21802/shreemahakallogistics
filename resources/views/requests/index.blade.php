@extends('layouts.app')

@section('title', 'Transport Requests')

@section('content')
    <header class="page-head">
        <div>
            <h1 class="page-title">Transport Requests</h1>
            <p class="page-meta">Customer requests, driver responses and follow-up in one place.</p>
        </div>
        <span class="page-count">{{ $requests->total() }} {{ Str::plural('request', $requests->total()) }}</span>
    </header>

    <form class="filters" method="GET" action="{{ route('requests.index') }}" role="search">
        <div class="filter filter--grow">
            <label class="filter__label" for="request-search">Search</label>
            <input
                class="filter__input"
                id="request-search"
                type="search"
                name="q"
                value="{{ $q }}"
                placeholder="Reference, customer or city"
                autocomplete="off"
            >
        </div>

        <div class="filter">
            <label class="filter__label" for="request-status">Status</label>
            <select class="filter__select" id="request-status" name="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter__actions">
            <button class="btn btn--quiet" type="submit">Apply filters</button>
            @if ($q !== '' || $status !== '')
                <a class="btn btn--quiet" href="{{ route('requests.index') }}">Clear</a>
            @endif
        </div>
    </form>

    <section class="panel" aria-label="Transport requests">
        @if ($requests->isEmpty())
            <div class="empty-state">
                <p class="empty-state__title">No requests found</p>
                <p class="empty-state__text">
                    @if ($q !== '' || $status !== '')
                        No requests match these filters.
                        <a class="text-link" href="{{ route('requests.index') }}">Clear filters</a>
                    @else
                        New customer requests will appear here as they arrive.
                    @endif
                </p>
            </div>
        @else
            <div class="table-scroll">
                <table class="data-table data-table--requests">
                    <thead>
                        <tr>
                            <th scope="col">Request</th>
                            <th scope="col">Route</th>
                            <th scope="col">Goods</th>
                            <th scope="col">Load</th>
                            <th scope="col">Received</th>
                            <th scope="col">Drivers</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr class="request-row" data-request-id="{{ $request->reference }}" data-pickup="{{ $request->pickup_city }}" data-destination="{{ $request->destination_city }}" data-goods="{{ $request->goods_category }}" data-weight="{{ number_format($request->weight_kg) }}" style="cursor:pointer">
                                <td>
                                    <span class="cell-ref">{{ $request->reference }}</span>
                                    <span class="cell-sub">{{ $request->customer_name }}</span>
                                </td>
                                <td>
                                    <span class="route">
                                        <span>{{ $request->pickup_city }}</span>
                                        <span class="route__arrow" aria-hidden="true">→</span>
                                        <span>{{ $request->destination_city }}</span>
                                    </span>
                                    <span class="cell-sub">{{ $request->pickup_address }} to {{ $request->destination_address }}</span>
                                </td>
                                <td>
                                    <span class="cell-title">{{ $request->goods_category }}@if ($request->is_fragile) · Fragile @endif</span>
                                    <span class="cell-sub cell-sub--truncate" title="{{ $request->goods_description }}">{{ $request->goods_description }}</span>
                                </td>
                                <td class="cell-nowrap">
                                    <span class="cell-title">{{ $request->quantity }}</span>
                                    <span class="cell-sub">{{ number_format($request->weight_kg) }} kg</span>
                                </td>
                                <td class="cell-nowrap">
                                    <time datetime="{{ $request->submitted_at->toIso8601String() }}" title="{{ $request->submitted_at->format('D, j M Y, H:i') }}">
                                        {{ $request->submitted_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td class="cell-nowrap">
                                    @if ($request->invited_count > 0)
                                        {{ $request->accepted_count }} of {{ $request->invited_count }} accepted
                                    @else
                                        <span class="cell-muted">Not sent</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$request->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$requests" />
        @endif
    </section>
    <!-- Find Drivers modal -->
    <div id="findDriversModal" class="fd-overlay" hidden>
        <div class="fd-modal" role="dialog" aria-modal="true" aria-labelledby="fd-title">
            <header class="fd-modal__head">
                <div>
                    <h2 id="fd-title" class="fd-modal__title">Find Drivers</h2>
                    <p class="fd-modal__sub" id="fd-sub">Request</p>
                </div>
                <button type="button" class="fd-close" data-fd-close aria-label="Close">×</button>
            </header>
            <div class="fd-toolbar">
                <label class="fd-label" for="fd-count">Drivers to consider</label>
                <select id="fd-count" class="fd-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="30">30</option>
                </select>
                <span class="fd-hint">Nearest to source shown first</span>
            </div>
            <div class="fd-body">
                <div class="fd-map-wrap">
                    <div id="fd-map" class="fd-map" aria-label="Map of source and nearby drivers"></div>
                </div>
                <aside class="fd-list-wrap">
                    <div class="fd-list-head">
                        <span>Nearest drivers</span>
                        <button type="button" class="fd-link" id="fd-select-all">Select all</button>
                    </div>
                    <ul id="fd-list" class="fd-list"></ul>
                </aside>
            </div>
            <footer class="fd-footer">
                <span id="fd-selected" class="fd-selected">0 selected</span>
                <button type="button" class="fd-send" id="fd-send">Send Request</button>
            </footer>
        </div>
    </div>
    <div id="fd-toast" class="fd-toast" hidden>
        <span id="fd-toast-text"></span>
        <button type="button" class="fd-toast-close" data-toast-close aria-label="Close notification">×</button>
    </div>

    <style>
        .request-row:hover { background: var(--color-brand-tint); }
        .fd-overlay { position: fixed; inset: 0; background: rgba(17,27,24,.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .fd-overlay[hidden] { display: none; }
        .fd-modal { background: #fff; border-radius: 16px; width: min(1100px, 100%); max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 12px 36px rgba(17,27,24,.12); }
        .fd-modal__head { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--color-border); }
        .fd-modal__title { font-size: 20px; font-weight: 650; margin: 0; color: var(--color-text-primary); }
        .fd-modal__sub { font-size: 13px; color: var(--color-text-secondary); margin: 4px 0 0; }
        .fd-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--color-text-secondary); }
        .fd-toolbar { display: flex; align-items: center; gap: 12px; padding: 14px 24px; border-bottom: 1px solid var(--color-border); }
        .fd-label { font-size: 13px; font-weight: 500; color: var(--color-text-secondary); }
        .fd-select { padding: 6px 10px; border: 1px solid var(--color-border); border-radius: 8px; font-size: 14px; }
        .fd-hint { font-size: 12px; color: var(--color-text-muted); margin-left: auto; }
        .fd-body { display: grid; grid-template-columns: 1fr 360px; gap: 0; flex: 1; min-height: 0; overflow: hidden; }
        .fd-map-wrap { padding: 16px; background: var(--color-surface-muted); }
        .fd-map { width: 100%; height: 100%; min-height: 360px; border-radius: 12px; background: #eef5f1; }
        .fd-list-wrap { border-left: 1px solid var(--color-border); display: flex; flex-direction: column; min-height: 0; }
        .fd-list-head { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--color-text-secondary); border-bottom: 1px solid var(--color-border); }
        .fd-link { background: none; border: none; color: var(--color-brand-primary); font-size: 13px; cursor: pointer; font-weight: 500; }
        .fd-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
        .fd-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-bottom: 1px solid var(--color-border); }
        .fd-item input { width: 16px; height: 16px; accent-color: var(--color-brand-primary); }
        .fd-item__info { flex: 1; min-width: 0; }
        .fd-item__name { font-size: 14px; font-weight: 600; color: var(--color-text-primary); }
        .fd-item__meta { font-size: 12px; color: var(--color-text-secondary); }
        .fd-item__dist { font-size: 13px; font-weight: 600; color: var(--color-brand-primary); white-space: nowrap; }
        .fd-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid var(--color-border); }
        .fd-selected { font-size: 13px; color: var(--color-text-secondary); }
        .fd-send { background: var(--color-brand-primary); color: #fff; border: none; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .fd-send:disabled { opacity: .5; cursor: not-allowed; }
        .fd-toast { position: fixed; top: 20px; right: 20px; background: #fff; border: 1px solid var(--color-border); border-left: 4px solid var(--color-brand-primary); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; box-shadow: 0 12px 36px rgba(17,27,24,.12); z-index: 1100; max-width: 360px; }
        .fd-toast[hidden] { display: none; }
        .fd-toast-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--color-text-secondary); }
        @media (max-width: 768px) { .fd-body { grid-template-columns: 1fr; } .fd-list-wrap { border-left: none; border-top: 1px solid var(--color-border); max-height: 240px; } }
    </style>

    <script>
    const GM_KEY = @json(env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY'));
    window.initFindDriversMap = function () {};
    (function () {
        const modal = document.getElementById('findDriversModal');
        const mapEl = document.getElementById('fd-map');
        const list = document.getElementById('fd-list');
        const countSel = document.getElementById('fd-count');
        const selectedEl = document.getElementById('fd-selected');
        const sendBtn = document.getElementById('fd-send');
        const sub = document.getElementById('fd-sub');
        const toast = document.getElementById('fd-toast');
        const toastText = document.getElementById('fd-toast-text');

        const cityCoords = {
            'Mumbai': { lat: 19.0760, lng: 72.8777 },
            'Pune': { lat: 18.5204, lng: 73.8567 },
            'Ujjain': { lat: 23.1765, lng: 75.7885 },
            'Indore': { lat: 22.7196, lng: 75.8577 },
            'Delhi': { lat: 28.7041, lng: 77.1025 },
            'Bangalore': { lat: 12.9716, lng: 77.5946 },
        };
        let SRC = { lat: 19.0760, lng: 72.8777 };
        let DST = { lat: 18.5204, lng: 73.8567 };

        let map = null, directionsRenderer = null, markers = [];
        let selected = new Set();

        function haversine(a, b) {
            const R = 6371, toRad = x => x * Math.PI / 180;
            const dLat = toRad(b.lat - a.lat), dLng = toRad(b.lng - a.lng);
            const x = Math.sin(dLat/2)**2 + Math.cos(toRad(a.lat))*Math.cos(toRad(b.lat))*Math.sin(dLng/2)**2;
            return (R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1-x))).toFixed(1);
        }

        function drivers(n) {
            const out = [];
            for (let i = 0; i < n; i++) {
                const km = [10, 20, 30][i % 3] + (i % 4) * 2;
                const angle = (i / n) * Math.PI * 2 + (i % 5) * 0.4;
                const deg = km / 111;
                const lat = SRC.lat + Math.cos(angle) * deg;
                const lng = SRC.lng + Math.sin(angle) * deg / Math.cos(SRC.lat * Math.PI / 180);
                const pos = { lat, lng };
                out.push({ id: 'DRV-' + (1000 + i), name: 'Driver ' + (i + 1), vehicle: ['Tata Ace','Mahindra Bolero','Ashok Leyland Dost','Eicher Pro'][i % 4], capacity: [1.5,2,3,4][i % 4] + ' t', dist: haversine(SRC, pos) + ' km', pos });
            }
            out.sort((a, b) => parseFloat(a.dist) - parseFloat(b.dist));
            return out;
        }

        function clearMarkers() {
            markers.forEach(m => m.setMap(null));
            markers = [];
            if (directionsRenderer) { directionsRenderer.setMap(null); directionsRenderer = null; }
        }

        function renderMap(ds) {
            if (typeof google === 'undefined' || !google.maps) {
                mapEl.textContent = 'Google Maps unavailable. Check API key.';
                return;
            }
            if (!map) {
                map = new google.maps.Map(mapEl, { center: SRC, zoom: 9, mapTypeControl: false, streetViewControl: false });
            }
            clearMarkers();
            const srcMarker = new google.maps.Marker({ position: SRC, map, title: 'Source', label: { text: 'S', color: '#fff' } });
            const dstMarker = new google.maps.Marker({ position: DST, map, title: 'Destination', label: { text: 'D', color: '#fff' } });
            markers.push(srcMarker, dstMarker);
            ds.forEach((d, i) => {
                const m = new google.maps.Marker({ position: d.pos, map, title: d.name, label: { text: 'T', color: '#fff' } });
                markers.push(m);
                if (i < 5) {
                    const dsrv2 = new google.maps.DirectionsService();
                    dsrv2.route({ origin: SRC, destination: d.pos, travelMode: google.maps.TravelMode.DRIVING, avoidHighways: false, avoidTolls: false }, (res, status) => {
                        if (status === 'OK' && res.routes[0]) {
                            const path = res.routes[0].overview_path;
                            const line = new google.maps.Polyline({ path, geodesic: true, strokeColor: (i === 0 ? '#111b18' : '#9aa6a0'), strokeOpacity: 0.9, strokeWeight: 4, map });
                            markers.push(line);
                        } else {
                            const line = new google.maps.Polyline({ path: [SRC, d.pos], geodesic: true, strokeColor: (i === 0 ? '#111b18' : '#9aa6a0'), strokeOpacity: 0.9, strokeWeight: 4, map });
                            markers.push(line);
                        }
                    });
                }
            });
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(SRC); bounds.extend(DST);
            ds.forEach(d => bounds.extend(d.pos));
            map.fitBounds(bounds);

            const dsrv = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ map, suppressMarkers: true, polylineOptions: { strokeColor: '#111b18', strokeWeight: 4 } });
            dsrv.route({ origin: SRC, destination: DST, travelMode: google.maps.TravelMode.DRIVING, avoidHighways: false, avoidTolls: false }, (res, status) => {
                if (status === 'OK') directionsRenderer.setDirections(res);
            });
        }

        function renderList(ds) {
            list.innerHTML = '';
            ds.forEach(d => {
                const li = document.createElement('li');
                li.className = 'fd-item';
                li.innerHTML = `<input type="checkbox" data-id="${d.id}"><div class="fd-item__info"><div class="fd-item__name">${d.name}</div><div class="fd-item__meta">${d.vehicle} · ${d.capacity}</div></div><div class="fd-item__dist">${d.dist}</div>`;
                list.appendChild(li);
            });
        }

        function render() {
            const n = parseInt(countSel.value, 10);
            const ds = drivers(n);
            renderMap(ds);
            renderList(ds);
            selected.clear();
            updateSelected();
        }

        function openModal(req) {
            SRC = cityCoords[req.pickup] || { lat: 19.0760, lng: 72.8777 };
            DST = cityCoords[req.destination] || SRC;
            sub.textContent = req.id + ' · ' + req.pickup + ' → ' + req.destination + ' · ' + req.goods + ' · ' + req.weight + ' kg';
            modal.hidden = false;
            render();
        }
        function closeModal() { modal.hidden = true; }

        document.querySelectorAll('.request-row').forEach(row => {
            row.addEventListener('click', () => {
                openModal({
                    id: row.dataset.requestId,
                    pickup: row.dataset.pickup,
                    destination: row.dataset.destination,
                    goods: row.dataset.goods,
                    weight: row.dataset.weight
                });
            });
        });
        modal.querySelectorAll('[data-fd-close]').forEach(b => b.addEventListener('click', closeModal));
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

        countSel.addEventListener('change', render);
        document.getElementById('fd-select-all').addEventListener('click', () => {
            list.querySelectorAll('input[type=checkbox]').forEach(cb => { cb.checked = true; selected.add(cb.dataset.id); });
            updateSelected();
        });
        list.addEventListener('change', e => {
            if (e.target.matches('input[type=checkbox]')) {
                if (e.target.checked) selected.add(e.target.dataset.id); else selected.delete(e.target.dataset.id);
                updateSelected();
            }
        });

        function updateSelected() {
            selectedEl.textContent = selected.size + ' selected';
            sendBtn.disabled = selected.size === 0;
        }

        sendBtn.addEventListener('click', () => {
            const n = selected.size;
            toastText.textContent = 'Request sent to ' + n + ' driver' + (n === 1 ? '' : 's');
            toast.hidden = false;
            closeModal();
            setTimeout(() => { toast.hidden = true; }, 4000);
        });
        toast.querySelector('[data-toast-close]').addEventListener('click', () => { toast.hidden = true; });

        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
    })();
    </script>
    @if (env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY'))
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('NEXT_PUBLIC_GOOGLE_MAPS_API_KEY') }}&callback=initFindDriversMap"></script>
    @endif
@endsection
