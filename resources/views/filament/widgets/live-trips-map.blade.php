@php
    $token = $mapboxToken ?? null;
    $pollingInterval = $this->getPollingInterval();
@endphp

<div @if ($pollingInterval) wire:poll.{{ $pollingInterval }} @endif class="space-y-4">
    @if (blank($token))
        <div class="rounded-lg border border-dashed border-primary-200/70 bg-primary-50/40 p-4 text-sm text-primary-900 dark:border-primary-500/40 dark:bg-primary-500/10 dark:text-primary-100">
            Mapbox token belum dikonfigurasi. Tambahkan <code>MAPBOX_TOKEN=</code> di file <code>.env</code> dan jalankan ulang aplikasi untuk menampilkan peta live.
        </div>
    @else
        <div
            wire:ignore
            x-data="{
                token: {{ Illuminate\Support\Js::from($token) }},
                trips: {{ Illuminate\Support\Js::from($trips) }},
                map: null,
                markers: {},
                init() {
                    if (! this.token || typeof mapboxgl === 'undefined') {
                        return;
                    }

                    mapboxgl.accessToken = this.token;
                    this.map = new mapboxgl.Map({
                        container: this.$refs.map,
                        style: 'mapbox://styles/mapbox/streets-v12',
                        center: this.getInitialCenter(),
                        zoom: this.trips.length ? 11 : 10,
                    });

                    this.map.addControl(new mapboxgl.NavigationControl());

                    this.renderMarkers(this.trips);

                    window.addEventListener('live-trips-map:update', (event) => {
                        const nextTrips = event.detail?.trips ?? [];

                        this.renderMarkers(nextTrips);
                    });
                },
                getInitialCenter() {
                    if (this.trips.length) {
                        const first = this.trips[0];

                        return [first.lng, first.lat];
                    }

                    return [118.015, -8.25];
                },
                renderMarkers(trips) {
                    this.trips = Array.isArray(trips) ? trips : [];

                    Object.values(this.markers).forEach(marker => marker.remove());
                    this.markers = {};

                    if (! this.map || ! this.trips.length) {
                        return;
                    }

                    const bounds = new mapboxgl.LngLatBounds();
                    let hasBounds = false;

                    this.trips.forEach(trip => {
                        if (typeof trip.lng !== 'number' || typeof trip.lat !== 'number') {
                            return;
                        }

                        const lastSeen = trip.last_seen_at
                            ? new Date(trip.last_seen_at).toLocaleString()
                            : 'Tidak diketahui';

                        const popupContent = `
                            <div class='space-y-1'>
                                <div class='font-semibold text-sm text-neutral-900'>${trip.hiker ?? 'Tanpa Nama'}</div>
                                <div class='text-xs text-neutral-600'>Rute: ${trip.route ?? '-'}</div>
                                <div class='text-xs text-neutral-600'>Status: ${trip.status ?? '-'}</div>
                                <div class='text-xs text-neutral-500'>Terakhir terlihat: ${lastSeen}</div>
                            </div>
                        `;

                        const marker = new mapboxgl.Marker({ color: '#2563eb' })
                            .setLngLat([trip.lng, trip.lat])
                            .setPopup(new mapboxgl.Popup({ offset: 12 }).setHTML(popupContent))
                            .addTo(this.map);

                        this.markers[trip.id] = marker;

                        bounds.extend([trip.lng, trip.lat]);
                        hasBounds = true;
                    });

                    if (! hasBounds) {
                        return;
                    }

                    if (this.trips.length === 1) {
                        this.map.flyTo({
                            center: bounds.getCenter(),
                            zoom: 12,
                            essential: true,
                        });
                    } else {
                        this.map.fitBounds(bounds, {
                            padding: 60,
                            maxZoom: 14,
                            duration: 1000,
                        });
                    }
                },
            }"
            x-init="init()"
            class="rounded-lg border border-gray-200 bg-white p-0 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
            <div x-ref="map" class="h-[460px] w-full rounded-lg"></div>
        </div>
    @endif
</div>

<div
    x-data="{ trips: {{ Illuminate\Support\Js::from($trips) }} }"
    x-effect="
        window.dispatchEvent(new CustomEvent('live-trips-map:update', { detail: { trips } }));
    "
    class="hidden"
></div>

@once
    @push('styles')
        <link
            rel="stylesheet"
            href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css"
        >
    @endpush

    @push('scripts')
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    @endpush
@endonce
