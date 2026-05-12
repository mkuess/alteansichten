<x-filament-panels::page>

    <div x-data="{ search: '' }">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; gap: 1rem;">
            <p style="color: var(--ink-3); font-size: 0.8125rem; margin: 0;">
                {{ $categories->count() }} Standort-Kategorien · in Phase 1.1 als Seeder gepflegt
            </p>
            <div style="position: relative;">
                <x-heroicon-o-magnifying-glass style="position: absolute; left: 0.625rem;
                    top: 50%; transform: translateY(-50%); width: 0.875rem; height: 0.875rem;
                    color: var(--ink-3);" />
                <input
                    type="text"
                    x-model="search"
                    placeholder="Kategorie suchen…"
                    style="padding: 0.4rem 0.75rem 0.4rem 2rem; border: 1px solid var(--line);
                           border-radius: 8px; background: var(--card); font-size: 0.8125rem;
                           color: var(--ink); outline: none; width: 200px;
                           font-family: 'Geist', system-ui, sans-serif;"
                    onfocus="this.style.borderColor='var(--accent)'"
                    onblur="this.style.borderColor='var(--line)'"
                />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
            @foreach($categories as $cat)
                <a href="{{ route('filament.admin.resources.categories.edit', $cat) }}"
                   x-show="search === '' || '{{ strtolower($cat->name) }}'.includes(search.toLowerCase())"
                   style="display: flex; align-items: center; gap: 0.75rem;
                          background: var(--card); border: 1px solid var(--line);
                          border-radius: 12px; padding: 0.75rem 1rem;
                          text-decoration: none; transition: border-color 0.15s;"
                   onmouseover="this.style.borderColor='var(--accent)'"
                   onmouseout="this.style.borderColor='var(--line)'">

                    <div style="flex-shrink: 0; width: 2.25rem; height: 2.25rem;
                                border-radius: 8px; display: flex; align-items: center;
                                justify-content: center; background: var(--accent-bg);">
                        @if(!empty($cat->icon))
                            <x-dynamic-component :component="'heroicon-o-' . $cat->icon"
                                style="width: 1rem; height: 1rem; color: var(--accent);" />
                        @else
                            <x-heroicon-o-map-pin style="width: 1rem; height: 1rem; color: var(--accent);" />
                        @endif
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--ink);
                                    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $cat->name }}
                        </div>
                        <div style="font-size: 0.7rem; color: var(--ink-3);
                                    font-family: 'JetBrains Mono', monospace;
                                    margin-top: 0.125rem; white-space: nowrap;
                                    overflow: hidden; text-overflow: ellipsis;">
                            /orte?cat={{ $cat->slug }}
                        </div>
                    </div>

                    <span style="flex-shrink: 0; font-size: 0.75rem; font-weight: 500;
                                 background: var(--accent-bg); color: var(--accent-ink);
                                 font-family: 'JetBrains Mono', monospace;
                                 border-radius: 999px; padding: 0.1rem 0.5rem;
                                 min-width: 1.75rem; text-align: center;">
                        {{ $cat->places_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
