<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        @if($municipalities->isEmpty())
            <p class="text-sm" style="color: var(--ink-3);">Noch keine Gemeinden mit Standorten.</p>
        @else
            <ul class="space-y-3">
                @foreach($municipalities as $municipality)
                    @php $pct = $max > 0 ? round(($municipality->places_count / $max) * 100) : 0; @endphp
                    <li>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium" style="color: var(--ink-2);">
                                {{ $municipality->name }}
                            </span>
                            <span class="text-xs font-mono" style="color: var(--ink-3); font-family: 'JetBrains Mono', monospace;">
                                {{ $municipality->places_count }}
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full" style="background: var(--line-2);">
                            <div class="h-1.5 rounded-full transition-all duration-300"
                                 style="width: {{ $pct }}%; background: var(--accent);"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
