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

    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background-color: rgba(255,255,255,0.6) !important;
    }

    .marker-cluster-small div,
    .marker-cluster-medium div,
    .marker-cluster-large div {
        background-color: rgba(255,255,255,0.9) !important;
        color: #1c1a17 !important;
        font-weight: 600 !important;
    }
</style>

<script>
window.karteApp = function(placesData, municipalitiesData) {
    return {
        places: placesData,
        municipalities: municipalitiesData,
        selected: null,
        selectedType: null,
        panelOpen: false,
        map: null,
        init: function() {
            var self = this;
            self.loadLeaflet(function() {
                self.$nextTick(function() { self.initMap(); });
            });
        },
        loadLeaflet: function(cb) {
            var self = this;
            function loadMarkerCluster(done) {
                var cssIds = [
                    ['markercluster-css',         'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css'],
                    ['markercluster-default-css',  'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css'],
                ];
                cssIds.forEach(function(pair) {
                    if (!document.getElementById(pair[0])) {
                        var link = document.createElement('link');
                        link.id = pair[0];
                        link.rel = 'stylesheet';
                        link.href = pair[1];
                        document.head.appendChild(link);
                    }
                });
                if (window.L.markerClusterGroup) { done(); return; }
                if (!document.getElementById('markercluster-js')) {
                    var s = document.createElement('script');
                    s.id = 'markercluster-js';
                    s.src = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js';
                    s.onload = done;
                    document.head.appendChild(s);
                } else {
                    var poll = setInterval(function() {
                        if (window.L.markerClusterGroup) { clearInterval(poll); done(); }
                    }, 50);
                }
            }
            if (window.L) { loadMarkerCluster(cb); return; }
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
                s.onload = function() { loadMarkerCluster(cb); };
                document.head.appendChild(s);
            } else {
                var poll = setInterval(function() {
                    if (window.L) { clearInterval(poll); loadMarkerCluster(cb); }
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

            var placeCluster = L.markerClusterGroup();
            var muniCluster  = L.markerClusterGroup({ disableClusteringAtZoom: 10 });

            self.places.forEach(function(place) {
                var marker = L.marker([place.lat, place.lng]);
                marker.bindTooltip(place.title, { permanent: false, direction: 'top' });
                marker.on('click', function() {
                    self.selected = place;
                    self.selectedType = 'place';
                    self.panelOpen = true;
                    setTimeout(function() {
                        if (self.map) { self.map.invalidateSize(); }
                    }, 250);
                });
                placeCluster.addLayer(marker);
            });

            self.municipalities.forEach(function(muni) {
                var muniIcon;
                if (muni.logo_path) {
                    muniIcon = L.divIcon({
                        className: '',
                        html: '<img src="/storage/' + muni.logo_path + '" style="width:40px;height:40px;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.3));" />',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -22]
                    });
                } else {
                    muniIcon = L.divIcon({
                        className: '',
                        html: '<div style="width:36px;height:36px;border-radius:50%;background:#6b7f56;border:2px solid #3f5234;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' +
                              muni.name.substring(0, 2).toUpperCase() +
                              '</div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18],
                        popupAnchor: [0, -20]
                    });
                }
                var marker = L.marker([muni.lat, muni.lng], { icon: muniIcon });
                marker.bindTooltip(muni.name, { permanent: false, direction: 'top' });
                marker.on('click', function() {
                    self.selected = muni;
                    self.selectedType = 'municipality';
                    self.panelOpen = true;
                    setTimeout(function() {
                        if (self.map) { self.map.invalidateSize(); }
                    }, 250);
                });
                muniCluster.addLayer(marker);
            });

            placeCluster.addTo(self.map);
            muniCluster.addTo(self.map);

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
        },
        lbOpen: false,
        lbIndex: 0,
        lbImages: function() {
            return (this.selected ? this.selected.media : []).filter(function(m) { return !!m.thumb_url; });
        },
        openLightbox: function(itemId) {
            var imgs = this.lbImages();
            var idx = -1;
            for (var i = 0; i < imgs.length; i++) {
                if (imgs[i].id === itemId) { idx = i; break; }
            }
            if (idx >= 0) { this.lbIndex = idx; this.lbOpen = true; }
        },
        closeLightbox: function() {
            this.lbOpen = false;
        },
        lbNext: function() {
            var imgs = this.lbImages();
            if (imgs.length > 1) { this.lbIndex = (this.lbIndex + 1) % imgs.length; }
        },
        lbPrev: function() {
            var imgs = this.lbImages();
            if (imgs.length > 1) { this.lbIndex = (this.lbIndex - 1 + imgs.length) % imgs.length; }
        }
    };
};
</script>

{{-- Outer wrapper: position relative so the panel can float over the map --}}
<div
    x-data="karteApp({{ Js::from($mapData) }}, {{ Js::from($municipalitiesData) }})"
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
                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug" x-text="selectedType === 'municipality' ? selected?.name : selected?.title"></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="selectedType === 'municipality' ? (selected?.postal_code ?? '') : (selected?.municipality ? selected.municipality + (selected.district ? ', ' + selected.district : '') : '')"></p>
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

        {{-- Large preview image (first media item with a file) — places only --}}
        <div
            x-show="selectedType === 'place' && selected?.media?.find(function(m){ return m.thumb_url; })"
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

        {{-- Place body --}}
        <div class="p-4 space-y-4" x-show="selected && selectedType === 'place'" style="display:none;">

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

                <p
                    x-show="!selected?.media || selected.media.length === 0"
                    class="text-sm text-gray-400 dark:text-gray-500 italic"
                >Keine Medien verknüpft.</p>

                <div class="space-y-3">
                    <template x-for="item in (selected?.media ?? [])" :key="item.id">
                        <div class="flex gap-3 items-start">
                            <div
                                class="flex-shrink-0 w-16 h-14 rounded overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                                :class="item.thumb_url ? 'cursor-pointer' : ''"
                                @click="item.thumb_url && openLightbox(item.id)"
                            >
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

        {{-- Municipality body --}}
        <div class="p-4 space-y-4" x-show="selected && selectedType === 'municipality'" style="display:none;">

            {{-- Coat of arms --}}
            <div x-show="selected?.logo_path" style="display:none;">
                <img
                    :src="'/storage/' + selected?.logo_path"
                    :alt="selected?.name"
                    style="height:60px;object-fit:contain;"
                />
            </div>

            {{-- Postal code --}}
            <div x-show="selected?.postal_code">
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Postleitzahl</p>
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selected?.postal_code"></p>
            </div>

            {{-- Summary --}}
            <div x-show="selected?.summary">
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Beschreibung</p>
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selected?.summary"></p>
            </div>

            {{-- Places count --}}
            <div>
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Standorte</p>
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="(selected?.places_count ?? 0) + ' Standorte'"></p>
            </div>

            {{-- Edit link --}}
            <div class="flex flex-wrap gap-x-4 gap-y-2">
                <a
                    :href="'/admin/municipalities/' + selected?.id + '/edit'"
                    class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Gemeinde bearbeiten
                </a>
            </div>

            {{-- Places list --}}
            <div
                class="border-t border-gray-100 dark:border-gray-800 pt-4"
                x-show="selected?.places && selected.places.length > 0"
            >
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-3">
                    Standorte in dieser Gemeinde
                </p>
                <div class="space-y-1">
                    <template x-for="place in (selected?.places ?? [])" :key="place.id">
                        <a
                            :href="'/admin/places/' + place.id + '/edit'"
                            class="block text-sm text-primary-600 dark:text-primary-400 hover:underline py-0.5 truncate"
                            x-text="place.title"
                        ></a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox overlay --}}
    <div
        x-show="lbOpen"
        @click.self="closeLightbox()"
        @keydown.escape.window="if(lbOpen) closeLightbox()"
        style="display: none; position: fixed; inset: 0; z-index: 5000; background: rgba(0,0,0,0.88);"
        class="flex items-center justify-center"
    >
        {{-- Close button --}}
        <button
            @click="closeLightbox()"
            style="position: absolute; top: 1rem; right: 1rem; z-index: 5010; color: #fff; background: rgba(0,0,0,0.4); border-radius: 9999px; width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center;"
            aria-label="Schließen"
        >
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        {{-- Counter --}}
        <div
            x-show="lbImages().length > 1"
            style="position: absolute; top: 1rem; left: 50%; transform: translateX(-50%); color: #fff; font-size: 0.8rem; background: rgba(0,0,0,0.4); padding: 0.2rem 0.75rem; border-radius: 9999px;"
            x-text="(lbIndex + 1) + ' / ' + lbImages().length"
        ></div>

        {{-- Prev button --}}
        <button
            x-show="lbImages().length > 1"
            @click.stop="lbPrev()"
            style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); z-index: 5010; color: #fff; background: rgba(0,0,0,0.4); border-radius: 9999px; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center;"
            aria-label="Vorheriges Bild"
        >
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        {{-- Main image --}}
        <img
            :src="lbImages()[lbIndex]?.thumb_url"
            :alt="lbImages()[lbIndex]?.title"
            style="max-width: 90vw; max-height: 85vh; object-fit: contain; border-radius: 0.5rem; box-shadow: 0 25px 60px rgba(0,0,0,0.5);"
        />

        {{-- Caption --}}
        <div
            x-show="lbImages()[lbIndex]?.title"
            style="position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%); color: #e5e7eb; font-size: 0.8rem; background: rgba(0,0,0,0.5); padding: 0.25rem 1rem; border-radius: 9999px; white-space: nowrap; max-width: 80vw; overflow: hidden; text-overflow: ellipsis;"
            x-text="[lbImages()[lbIndex]?.title, lbImages()[lbIndex]?.year].filter(Boolean).join(' · ')"
        ></div>

        {{-- Next button --}}
        <button
            x-show="lbImages().length > 1"
            @click.stop="lbNext()"
            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 5010; color: #fff; background: rgba(0,0,0,0.4); border-radius: 9999px; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center;"
            aria-label="Nächstes Bild"
        >
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
</div>

</x-filament-panels::page>
