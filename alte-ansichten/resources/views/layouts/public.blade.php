<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Alte Ansichten')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #faf9f7;
            color: #2c2c2c;
            line-height: 1.7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { color: #5a3e2b; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Header */
        header {
            background: #3b2a1a;
            color: #f5f0e8;
            padding: 1.25rem 1.5rem;
        }
        header .inner {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        header .site-name {
            font-size: 1.35rem;
            font-weight: bold;
            letter-spacing: 0.02em;
            color: #f5f0e8;
        }
        header nav {
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
        }
        header nav a {
            color: #d4c9b8;
            font-size: 0.9rem;
            font-family: system-ui, sans-serif;
        }
        header nav a:hover { color: #fff; text-decoration: none; }

        /* Main */
        main {
            flex: 1;
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* Footer */
        footer {
            background: #3b2a1a;
            color: #a89880;
            text-align: center;
            padding: 1rem 1.5rem;
            font-size: 0.8rem;
            font-family: system-ui, sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body>

<header>
    <div class="inner">
        <span class="site-name">Alte Ansichten</span>
        <nav aria-label="Hauptnavigation">
            <a href="{{ url('/gemeinden') }}">Gemeinden</a>
            <a href="{{ url('/orte') }}">Orte</a>
            <a href="{{ url('/beitrag-einreichen') }}">Beitrag einreichen</a>
            <a href="{{ url('/problem-melden') }}">Problem melden</a>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer>
    <p>&copy; {{ date('Y') }} Alte Ansichten &mdash; Historisches Archiv für Gemeinden, Orte, Bilder und Geschichten.</p>
</footer>

@stack('scripts')
</body>
</html>
