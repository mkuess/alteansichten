@php
    $record = $getRecord();
    $lat = $record?->latitude ? (float) $record->latitude : null;
    $lng = $record?->longitude ? (float) $record->longitude : null;
    $hasCoords = $lat !== null && $lng !== null;
@endphp

@if($hasCoords)
@php
    $osmUrl = "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}#map=15/{$lat}/{$lng}";
@endphp
<div
    x-data="{
        map: null,
        marker: null,
        init() {
            this.loadLeaflet(() => this.$nextTick(() => this.initMap()));
        },
        destroy() {
            if (this.map) { this.map.remove(); this.map = null; }
        },
        loadLeaflet(cb) {
            if (window.L) { cb(); return; }
            if (!document.getElementById('leaflet-css')) {
                const link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            if (!document.getElementById('leaflet-js')) {
                const s = document.createElement('script');
                s.id = 'leaflet-js';
                s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                s.onload = cb;
                document.head.appendChild(s);
            } else {
                const poll = setInterval(() => {
                    if (window.L) { clearInterval(poll); cb(); }
                }, 50);
            }
        },
        initMap() {
            if (this.map) { this.map.remove(); }
            this.map = L.map(this.$refs.mapEl).setView([{{ $lat }}, {{ $lng }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(this.map);
            this.marker = L.marker([{{ $lat }}, {{ $lng }}], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                $wire.set('data.latitude', pos.lat.toFixed(7));
                $wire.set('data.longitude', pos.lng.toFixed(7));
            });
        }
    }"
    class="space-y-2"
>
    <p class="text-xs text-gray-500 dark:text-gray-400">
        Pin verschieben, um die Koordinaten manuell anzupassen.
    </p>
    <div
        x-ref="mapEl"
        style="height:240px;border-radius:0.5rem;border:1px solid #d1d5db;z-index:0;"
    ></div>
    <a
        href="{{ $osmUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="text-sm text-primary-600 dark:text-primary-400 hover:underline inline-block"
    >↗ In OpenStreetMap öffnen</a>
</div>
@else
<p class="text-sm text-gray-500 dark:text-gray-400 italic">
    Noch keine Koordinaten hinterlegt.
</p>
@endif
