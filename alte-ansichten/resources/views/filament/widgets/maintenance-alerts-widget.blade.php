<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        <ul class="space-y-2">
            @foreach($alerts as $alert)
                @php
                    $colors = [
                        'success' => ['bg' => 'var(--accent-bg)',  'border' => 'var(--accent)',  'text' => 'var(--accent-ink)'],
                        'warning' => ['bg' => 'var(--amber-bg)',   'border' => 'var(--amber)',   'text' => '#7a5a20'],
                        'info'    => ['bg' => 'var(--line-2)',     'border' => 'var(--line)',    'text' => 'var(--ink-2)'],
                        'danger'  => ['bg' => 'var(--rose-bg)',    'border' => 'var(--rose)',    'text' => '#7a3828'],
                    ];
                    $c = $colors[$alert['level']] ?? $colors['info'];
                @endphp
                <li class="flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm"
                    style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['border'] }}20;">
                    <div class="flex items-center gap-2 min-w-0">
                        @svg($alert['icon'], 'w-4 h-4 flex-shrink-0', ['style' => 'color: ' . $c['text']])
                        <span class="truncate" style="color: {{ $c['text'] }};">{{ $alert['message'] }}</span>
                    </div>
                    @if($alert['action'])
                        <a href="{{ $alert['action'] }}"
                           class="flex-shrink-0 text-xs font-medium rounded px-2 py-0.5"
                           style="color: {{ $c['text'] }}; border: 1px solid {{ $c['border'] }}40; white-space: nowrap;">
                            {{ $alert['label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
