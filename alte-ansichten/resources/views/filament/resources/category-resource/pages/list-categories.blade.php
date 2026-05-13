<style>
#cat-grid {
    display: grid;
    gap: 0.75rem;
    grid-template-columns: repeat(4, 1fr);
}
@media (max-width: 1024px) {
    #cat-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    #cat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    #cat-grid { grid-template-columns: repeat(1, 1fr); }
}
</style>

<x-filament-panels::page>

<div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
    <p style="color: var(--ink-3); font-size: 0.8125rem; margin: 0;">
        {{ $categories->count() }} Standort-Kategorien · in Phase 1.1 als Seeder gepflegt
    </p>
    <div style="position: relative;">
        <x-heroicon-o-magnifying-glass style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); width: 0.875rem; height: 0.875rem; color: var(--ink-3);" />
        <input
            type="text"
            id="cat-search"
            placeholder="Kategorie suchen…"
            style="padding: 0.4rem 0.75rem 0.4rem 2rem; border: 1px solid var(--line); border-radius: 8px; background: var(--card); font-size: 0.8125rem; color: var(--ink); outline: none; width: 200px;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='var(--line)'"
            oninput="filterCats(this.value)"
        />
    </div>
</div>

<div id="cat-grid" style="gap: 0.75rem;">
    @foreach($categories as $cat)
        <a href="{{ route('filament.admin.resources.categories.edit', $cat) }}"
           data-name="{{ strtolower($cat->name) }}"
           style="display: flex; flex-direction: row; align-items: center; gap: 0.75rem;
                  background: var(--card); border: 1px solid var(--line);
                  border-radius: 12px; padding: 0.75rem 1rem;
                  text-decoration: none;"
           onmouseover="this.style.borderColor='var(--accent)'"
           onmouseout="this.style.borderColor='var(--line)'">

            <div style="flex-shrink: 0; width: 2.25rem; height: 2.25rem; border-radius: 8px;
                        display: flex; align-items: center; justify-content: center;
                        background: var(--accent-bg);">
                <x-heroicon-o-map-pin style="width: 1rem; height: 1rem; color: var(--accent);" />
            </div>

            <div style="flex: 1; min-width: 0;">
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--ink);
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $cat->name }}
                </div>
                <div style="font-size: 0.7rem; color: var(--ink-3);
                            font-family: 'JetBrains Mono', monospace; margin-top: 0.125rem;
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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

<script>
function filterCats(val) {
    document.querySelectorAll('#cat-grid a').forEach(function(card) {
        var name = card.getAttribute('data-name') || '';
        card.style.display = name.includes(val.toLowerCase()) ? 'flex' : 'none';
    });
}
</script>

</x-filament-panels::page>
