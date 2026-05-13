<div style="padding: 0.25rem 0.625rem 0.75rem;">
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
        @csrf
        <button
            type="submit"
            style="
                display: flex;
                align-items: center;
                gap: 0.5rem;
                width: 100%;
                border-radius: 8px;
                padding: 0.3125rem 0.625rem;
                font-size: 0.8125rem;
                font-weight: 500;
                color: var(--ink-3);
                background: none;
                border: none;
                cursor: pointer;
                text-align: left;
                transition: background 0.1s, color 0.1s;
            "
            onmouseover="this.style.background='var(--line-2)';this.style.color='var(--ink-2)'"
            onmouseout="this.style.background='none';this.style.color='var(--ink-3)'"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 style="width:1.0625rem;height:1.0625rem;flex-shrink:0;"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
            </svg>
            Abmelden
        </button>
    </form>
</div>
