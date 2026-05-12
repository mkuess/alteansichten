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

<script>
window.placeMapPicker = function(lat, lng) {
    return {
        map: null,
        marker: null,
        init: function() {
            var self = this;
            self.loadLeaflet(function() {
                self.$nextTick(function() { self.initMap(); });
            });
        },
        destroy: function() {
            if (this.map) { this.map.remove(); this.map = null; }
        },
        loadLeaflet: function(cb) {
            if (window.L) { cb(); return; }
            if (!document.getElementById('leaflet-css')) {
                var link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            if (!document.getElementById('leaflet-js')) {
                var s = document.createElement('script');
                s.id = 'leaflet-js';
                s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                s.onload = cb;
                document.head.appendChild(s);
            } else {
                var poll = setInterval(function() {
                    if (window.L) { clearInterval(poll); cb(); }
                }, 50);
            }
        },
        initMap: function() {
            var self = this;
            if (self.map) { self.map.remove(); }
            self.map = L.map(self.$refs.mapEl).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '\u00a9 OpenStreetMap contributors'
            }).addTo(self.map);
            self.marker = L.marker([lat, lng], { draggable: true }).addTo(self.map);
            self.marker.on('dragend', function(e) {
                var pos = e.target.getLatLng();
                self.$wire.set('data.latitude', pos.lat.toFixed(7));
                self.$wire.set('data.longitude', pos.lng.toFixed(7));
            });
        }
    };
};
</script>

<div x-data="placeMapPicker({{ $lat }}, {{ $lng }})" class="space-y-2">
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
    >&#8599; In OpenStreetMap öffnen</a>
</div>
@else
<p class="text-sm text-gray-500 dark:text-gray-400 italic">
    Noch keine Koordinaten hinterlegt.
</p>
@endif
