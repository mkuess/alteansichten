@php
    $user = filament()->auth()->user();
@endphp
@if($user)
<div class="px-3 py-2 mb-1">
    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5"
         style="background: rgba(255,255,255,0.55); border: 1px solid var(--line);">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm"
             style="background: var(--accent-bg); color: var(--accent-ink); font-family: 'Newsreader', Georgia, serif; font-style: italic;">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium truncate" style="color: var(--ink);">{{ $user->name }}</div>
            <div class="text-xs truncate" style="color: var(--ink-3); font-family: 'JetBrains Mono', monospace;">
                {{ $user->role ?? $user->email }}
            </div>
        </div>
    </div>
</div>
@endif
