<x-filament-panels::page>
    <p class="text-sm" style="color: var(--ink-3); margin-bottom: 1rem;">
        {{ $categories->count() }} Standort-Kategorien · in Phase 1.1 als Seeder gepflegt
    </p>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $cat)
            <a href="{{ route('filament.admin.resources.categories.edit', $cat) }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition"
               style="background: var(--card); border: 1px solid var(--line);">

                <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center"
                     style="background: var(--accent-bg);">
                    <x-heroicon-o-map-pin class="w-4 h-4" style="color: var(--accent);" />
                </div>

                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate" style="color: var(--ink);">
                        {{ $cat->name }}
                    </div>
                    <div class="text-xs truncate mt-0.5"
                         style="color: var(--ink-3); font-family: 'JetBrains Mono', monospace;">
                        /orte?cat={{ $cat->slug }}
                    </div>
                </div>

                <span class="flex-shrink-0 text-xs font-medium rounded-full px-2 py-0.5"
                      style="background: var(--accent-bg); color: var(--accent-ink);
                             font-family: 'JetBrains Mono', monospace; min-width: 1.75rem; text-align: center;">
                    {{ $cat->places_count }}
                </span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
