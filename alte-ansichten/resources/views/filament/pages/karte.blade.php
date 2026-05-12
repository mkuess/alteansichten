<x-filament-panels::page>

{{-- Strip all surrounding chrome padding for this page only --}}
<style>
    .fi-header { display: none !important; }
    .fi-page-header { display: none !important; }
    .fi-simple-page-header { display: none !important; }
    .fi-main { padding-left: 0 !important; padding-right: 0 !important; padding-bottom: 0 !important; }
    .fi-page > section { padding-top: 0 !important; padding-bottom: 0 !important; gap: 0 !important; }
    .fi-page > section > div { gap: 0 !important; }
    .fi-page > section > div > div { gap: 0 !important; }
</style>

<script>
window.karteApp = function(placesData) {
    return {
        places: placesData,
        selected: null,
        panelOpen: false,
        map: null,
        init: function() {
            var self = this;
            self.loadLeaflet(function() {
                self.$nextTick(function() { self.initMap(); });
            });
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
            var defaultCenter = [47.2, 14.0];
            var defaultZoom = 8;

            if (self.places.length === 1) {
                defaultCenter = [self.places[0].lat, self.places[0].lng];
                defaultZoom = 14;
            } else if (self.places.length > 1) {
                var lats = self.places.map(function(p) { return p.lat; });
                var lngs = self.places.map(function(p) { return p.lng; });
                defaultCenter = [
                    (Math.min.apply(null, lats) + Math.max.apply(null, lats)) / 2,
                    (Math.min.apply(null, lngs) + Math.max.apply(null, lngs)) / 2
                ];
            }

            self.map = L.map(self.$refs.mapEl).setView(defaultCenter, defaultZoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '\u00a9 OpenStreetMap contributors'
            }).addTo(self.map);

            self.places.forEach(function(place) {
                var marker = L.marker([place.lat, place.lng]).addTo(self.map);
                marker.bindTooltip(place.title, { permanent: false, direction: 'top' });
                marker.on('click', function() {
                    self.selected = place;
                    self.panelOpen = true;
                    setTimeout(function() {
                        if (self.map) { self.map.invalidateSize(); }
                    }, 250);
                });
            });

            setTimeout(function() {
                if (self.map) { self.map.invalidateSize(); }
            }, 300);
        },
        closePanel: function() {
            this.panelOpen = false;
            var self = this;
            setTimeout(function() {
                if (self.map) { self.map.invalidateSize(); }
            }, 250);
        },
        addressLine: function(place) {
            if (!place) return '';
            var parts = [];
            if (place.street) {
                parts.push(place.street + (place.house_number ? ' ' + place.house_number : ''));
            }
            var locality = [];
            if (place.postal_code) locality.push(place.postal_code);
            if (place.municipality) locality.push(place.municipality);
            if (locality.length) parts.push(locality.join(' '));
            if (place.address_text && !place.street) parts.push(place.address_text);
            return parts.join(', ') || '';
        },
        hasAddress: function(place) {
            if (!place) return false;
            return !!(place.street || place.postal_code || place.address_text);
        }
    };
};
</script>

{{-- Outer wrapper: position relative so the panel can float over the map --}}
<div
    x-data="karteApp({{ Js::from($mapData) }})"
    class="relative overflow-hidden"
    style="height: calc(100vh - 3.5rem); width: 100%;"
>
    {{-- Map fills the full space --}}
    <div wire:ignore style="position: absolute; inset: 0; z-index: 0;">
        <div
            x-ref="mapEl"
            style="width: 100%; height: 100%;"
        ></div>
    </div>

    {{-- Empty state when no places have coordinates --}}
    @if(count($mapData) === 0)
    <div class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-gray-900 z-10">
        <p class="text-sm text-gray-400 dark:text-gray-500 italic">
            Keine Standorte mit Koordinaten vorhanden.
        </p>
    </div>
    @endif

    {{-- Slide-in detail panel --}}
    <div
        x-show="panelOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        style="display: none; position: absolute; top: 0; right: 0; bottom: 0; width: 22rem; z-index: 2000;"
        class="bg-white dark:bg-gray-900 shadow-2xl border-l border-gray-200 dark:border-gray-700 overflow-y-auto"
    >
        {{-- Sticky header --}}
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug" x-text="selected?.title"></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="selected?.municipality ? selected.municipality + (selected.district ? ', ' + selected.district : '') : ''"></p>
            </div>
            <button
                @click="closePanel()"
                class="flex-shrink-0 mt-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                aria-label="Schließen"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Large preview image (first media item with a file) --}}
        <div
            x-show="selected?.media?.find(function(m){ return m.thumb_url; })"
            style="display: none;"
        >
            <img
                :src="selected?.media?.find(function(m){ return m.thumb_url; })?.thumb_url"
                :alt="selected?.title"
                class="w-full object-cover"
                style="height: 13rem;"
                loading="lazy"
            />
        </div>

        {{-- Body --}}
        <div class="p-4 space-y-4" x-show="selected">

            {{-- Address --}}
            <div x-show="selected && hasAddress(selected)">
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Adresse</p>
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selected ? addressLine(selected) : ''"></p>
            </div>

            {{-- Coordinates --}}
            <div>
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Koordinaten</p>
                <p class="text-xs font-mono text-gray-600 dark:text-gray-400" x-text="selected?.lat?.toFixed(6) + ', ' + selected?.lng?.toFixed(6)"></p>
            </div>

            {{-- Action links --}}
            <div class="flex flex-wrap gap-x-4 gap-y-2">
                <a
                    :href="selected?.edit_url"
                    class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Standort bearbeiten
                </a>
                <a
                    :href="selected?.create_media_url"
                    class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Medium hinzufügen
                </a>
            </div>

            {{-- Media section --}}
            <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-3">
                    Medien (<span x-text="selected?.media_count ?? 0"></span>)
                </p>

                {{-- No media --}}
                <p
                    x-show="!selected?.media || selected.media.length === 0"
                    class="text-sm text-gray-400 dark:text-gray-500 italic"
                >Keine Medien verknüpft.</p>

                {{-- Media list --}}
                <div class="space-y-3">
                    <template x-for="item in (selected?.media ?? [])" :key="item.id">
                        <div class="flex gap-3 items-start">
                            {{-- Thumbnail --}}
                            <div class="flex-shrink-0 w-16 h-14 rounded overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <img
                                    x-show="item.thumb_url"
                                    :src="item.thumb_url"
                                    :alt="item.title"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                                <div
                                    x-show="!item.thumb_url"
                                    class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="item.title"></p>
                                <p
                                    class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"
                                    x-text="[item.year, item.status].filter(Boolean).join(' · ')"
                                ></p>
                                <a
                                    x-show="item.edit_url"
                                    :href="item.edit_url"
                                    class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline mt-1"
                                >Bearbeiten</a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

</x-filament-panels::page>
