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
                self.$nextTick(function() {
                    self.initMap();
                    // Fix partial tile rendering: invalidate after layout settles
                    setTimeout(function() {
                        if (self.map) { self.map.invalidateSize(); }
                    }, 250);
                    setTimeout(function() {
                        if (self.map) { self.map.invalidateSize(); }
                    }, 600);
                });
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
            if (self.map) { self.map.remove(); self.map = null; }
            self.map = L.map(self.$refs.mapEl).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '\u00a9 OpenStreetMap contributors'
            }).addTo(self.map);
            self.marker = L.marker([lat, lng], { draggable: true }).addTo(self.map);
            self.marker.on('dragend', function(e) {
                var pos = e.target.getLatLng();
                var newLat = pos.lat.toFixed(7);
                var newLng = pos.lng.toFixed(7);

                // Update inputs directly — avoids a Livewire round-trip that
                // would morph the DOM and destroy the map instance.
                var latInput = document.querySelector('input[wire\\:model\\.live="data.latitude"], input[wire\\:model="data.latitude"]');
                var lngInput = document.querySelector('input[wire\\:model\\.live="data.longitude"], input[wire\\:model="data.longitude"]');

                if (latInput) {
                    latInput.value = newLat;
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    latInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (lngInput) {
                    lngInput.value = newLng;
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                // Keep map alive after the event
                setTimeout(function() {
                    if (self.map) { self.map.invalidateSize(); }
                }, 150);
            });
        }
    };
};
</script>

<div class="space-y-2">
    <p class="text-xs text-gray-500 dark:text-gray-400">
        Pin verschieben, um die Koordinaten manuell anzupassen.
    </p>
    {{-- wire:ignore tells Livewire to never morph this element,
         so the Leaflet instance survives any Livewire re-renders. --}}
    <div wire:ignore>
        <div
            x-data="placeMapPicker({{ $lat }}, {{ $lng }})"
            x-ref="mapWrapper"
        >
            <div
                x-ref="mapEl"
                style="height:320px;width:100%;border-radius:0.5rem;border:1px solid #d1d5db;z-index:0;"
            ></div>
        </div>
    </div>
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
