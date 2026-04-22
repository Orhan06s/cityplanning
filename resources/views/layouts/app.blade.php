<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <header>
        <div class="header-inner">
            <div>
                <h1> Metropolis</h1>
            </div>
            <div class="taal-knop">
                @if(app()->getLocale() === 'nl')
                    <a href="{{ route('taal', 'en') }}" class="taal-btn"> English</a>
                @else
                    <a href="{{ route('taal', 'nl') }}" class="taal-btn"> Nederlands</a>
                @endif
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="melding">{{ session('success') }}</div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>
        <p>Metropolis Simulator &copy; {{ date('Y') }}</p>
    </footer>

    <script src="{{ asset('js/metropolis.js') }}?v={{ filemtime(public_path('js/metropolis.js')) }}"></script>
</body>
</html>
