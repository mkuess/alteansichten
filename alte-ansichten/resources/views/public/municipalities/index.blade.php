@extends('layouts.public')

@section('title', 'Gemeinden – Alte Ansichten')

@section('content')
<style>
    .page-header {
        border-bottom: 1px solid #ddd8cf;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    .page-header h1 {
        font-size: 1.75rem;
        color: #3b2a1a;
        margin-bottom: 0.5rem;
    }
    .page-header p {
        font-size: 0.95rem;
        color: #4a4035;
        max-width: 580px;
    }
    .back-link {
        display: inline-block;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        font-family: system-ui, sans-serif;
        color: #5a3e2b;
    }

    .municipality-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .municipality-item {
        background: #fff;
        border: 1px solid #ddd8cf;
        border-radius: 4px;
        padding: 1rem 1.125rem;
    }
    .municipality-item a {
        font-size: 1rem;
        font-weight: bold;
        color: #3b2a1a;
        display: block;
        margin-bottom: 0.25rem;
    }
    .municipality-item a:hover { text-decoration: underline; }
    .municipality-meta {
        font-size: 0.8rem;
        font-family: system-ui, sans-serif;
        color: #7a6a58;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1rem;
        margin-bottom: 0.3rem;
    }
    .municipality-summary {
        font-size: 0.875rem;
        color: #4a4035;
        margin-top: 0.3rem;
    }

    .empty-state {
        background: #f0ebe2;
        border: 1px solid #ddd8cf;
        border-radius: 4px;
        padding: 2rem 1.5rem;
        text-align: center;
        color: #5a5048;
        font-family: system-ui, sans-serif;
        font-size: 0.9rem;
    }
    .empty-state p + p { margin-top: 0.5rem; }
</style>

<a class="back-link" href="{{ url('/') }}">&larr; Zurück zur Startseite</a>

<div class="page-header">
    <h1>Gemeinden</h1>
    <p>
        Alle österreichischen Gemeinden, die im Archiv vertreten sind –
        mit historischen Standorten, Bildern und Geschichten.
    </p>
</div>

<section aria-label="Gemeindenübersicht">
    @if ($municipalities->isEmpty())
        <div class="empty-state">
            <p>Noch keine Gemeinden im Archiv verfügbar.</p>
            <p>Bitte schauen Sie bald wieder vorbei oder <a href="{{ url('/beitrag-einreichen') }}">reichen Sie einen Beitrag ein</a>.</p>
        </div>
    @else
        <ul class="municipality-list">
            @foreach ($municipalities as $municipality)
                <li class="municipality-item">
                    <a href="{{ url('/gemeinden/' . $municipality->slug) }}">
                        {{ $municipality->name }}
                    </a>
                    <div class="municipality-meta">
                        @if ($municipality->postal_code)
                            <span>PLZ {{ $municipality->postal_code }}</span>
                        @endif
                        @if ($municipality->district)
                            <span>{{ $municipality->district->name }}</span>
                        @endif
                    </div>
                    @if ($municipality->summary)
                        <p class="municipality-summary">{{ $municipality->summary }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
