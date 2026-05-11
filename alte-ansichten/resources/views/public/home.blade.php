@extends('layouts.public')

@section('title', 'Alte Ansichten – Historisches Archiv')

@section('content')
<style>
    .hero {
        border-bottom: 1px solid #ddd8cf;
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }
    .hero h1 {
        font-size: 2rem;
        line-height: 1.25;
        color: #3b2a1a;
        margin-bottom: 0.75rem;
    }
    .hero p {
        font-size: 1.05rem;
        color: #4a4035;
        max-width: 600px;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }
    .feature-card {
        background: #fff;
        border: 1px solid #ddd8cf;
        border-radius: 4px;
        padding: 1.25rem 1rem;
    }
    .feature-card h2 {
        font-size: 1rem;
        color: #3b2a1a;
        margin-bottom: 0.35rem;
    }
    .feature-card p {
        font-size: 0.875rem;
        color: #5a5048;
        font-family: system-ui, sans-serif;
        line-height: 1.5;
    }
    .feature-card a {
        display: inline-block;
        margin-top: 0.6rem;
        font-size: 0.85rem;
        font-family: system-ui, sans-serif;
        color: #5a3e2b;
    }

    .contribute {
        background: #f0ebe2;
        border: 1px solid #ddd8cf;
        border-radius: 4px;
        padding: 1.25rem 1rem;
    }
    .contribute h2 {
        font-size: 1rem;
        color: #3b2a1a;
        margin-bottom: 0.4rem;
    }
    .contribute p {
        font-size: 0.875rem;
        color: #4a4035;
        font-family: system-ui, sans-serif;
        margin-bottom: 0.75rem;
    }
    .contribute .links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .contribute .links a {
        font-size: 0.875rem;
        font-family: system-ui, sans-serif;
    }
</style>

<section class="hero">
    <h1>Historisches Archiv für Gemeinden, Orte, Bilder und Geschichten</h1>
    <p>
        Alte Ansichten sammelt und bewahrt historische Aufnahmen, Ortsgeschichten und Erinnerungen
        aus österreichischen Gemeinden. Stöbern Sie in historischen Standorten oder tragen Sie
        selbst zur Sammlung bei.
    </p>
</section>

<section aria-label="Übersicht">
    <div class="feature-grid">
        <div class="feature-card">
            <h2>Gemeinden</h2>
            <p>Entdecken Sie historische Inhalte geordnet nach österreichischen Gemeinden.</p>
            <a href="{{ url('/gemeinden') }}">Alle Gemeinden ansehen &rarr;</a>
        </div>
        <div class="feature-card">
            <h2>Orte &amp; Standorte</h2>
            <p>Gasthäuser, Schulen, Bahnhöfe, Sakralbauten und viele weitere historische Standorte.</p>
            <a href="{{ url('/orte') }}">Orte durchsuchen &rarr;</a>
        </div>
    </div>
</section>

<section class="contribute" aria-label="Mitmachen">
    <h2>Mitmachen</h2>
    <p>
        Sie kennen einen historischen Ort oder haben alte Fotos? Tragen Sie zur Sammlung bei
        oder melden Sie einen Fehler.
    </p>
    <div class="links">
        <a href="{{ url('/beitrag-einreichen') }}">Beitrag einreichen &rarr;</a>
        <a href="{{ url('/problem-melden') }}">Problem melden &rarr;</a>
    </div>
</section>
@endsection
