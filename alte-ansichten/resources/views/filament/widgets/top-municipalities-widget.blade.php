<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        @if($municipalities->isEmpty())
            <p class="text-sm" style="color: var(--ink-3)">Noch keine Gemeinden angelegt.</p>
        @else
            <ul class="space-y-3">
                @foreach($municipalities as $m)
                    @php $pct = $max > 0 ? round(($m->places_count / $max) * 100) : 0; @endphp
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-md flex items-center justify-center text-xs"
                             style="border:1px solid var(--line); background: var(--bg); font-family: 'Newsreader', Georgia, serif; font-style: italic;">
                            {{ strtoupper(substr($m->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate" style="color: var(--ink-2);">{{ $m->name }}</div>
                            <div class="h-1 rounded-full mt-1" style="background: var(--line-2)">
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background: var(--accent);"></div>
                            </div>
                        </div>
                        <span class="text-xs" style="color: var(--ink-3); font-family: 'JetBrains Mono', monospace;">
                            {{ $m->places_count }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
