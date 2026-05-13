@php
    $initial = $user ? strtoupper(substr($user->name, 0, 1)) : '?';
    $name    = $user?->name ?? '—';
    $email   = $user?->email ?? '';
@endphp

<div class="user-card-footer" style="
    border-top: 1px solid var(--line);
    background: rgba(0,0,0,0.06);
    padding: 0 0.625rem;
    height: 54px;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex-shrink: 0;
">
    {{-- Avatar circle --}}
    <div class="user-card-avatar" style="
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent-bg);
        color: var(--accent-ink);
        font-size: 0.8125rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        letter-spacing: 0;
    ">{{ $initial }}</div>

    {{-- Name / email --}}
    <div class="user-card-text" style="flex: 1; min-width: 0; line-height: 1.25;">
        <div style="
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        ">{{ $name }}</div>
        <div style="
            font-size: 0.6875rem;
            color: var(--ink-3);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        ">{{ $email }}</div>
    </div>

    {{-- Logout icon button --}}
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="flex-shrink: 0;">
        @csrf
        <button
            type="submit"
            title="Abmelden"
            style="
                width: 30px;
                height: 30px;
                border-radius: 6px;
                border: none;
                background: none;
                color: var(--ink-3);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.1s, color 0.1s;
            "
            onmouseover="this.style.background='var(--line-2)';this.style.color='var(--ink)'"
            onmouseout="this.style.background='none';this.style.color='var(--ink-3)'"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 style="width:1rem;height:1rem;"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
            </svg>
        </button>
    </form>
</div>
