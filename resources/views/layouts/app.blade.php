<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <style>
        .user-info { display:flex;align-items:center;gap:.6rem;font-size:.88rem; }
        .user-badge { border-radius:999px;padding:.18rem .65rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em; }
        .user-badge.admin { background:#e0e7ff;color:#3730a3; }
        .user-badge.gebruiker { background:#fef3c7;color:#92400e; }
        .user-badge.viewer { background:#dcfce7;color:#166534; }
        .btn-logout { background:transparent;border:1.5px solid rgba(255,255,255,.4);border-radius:6px;padding:.28rem .75rem;font-size:.82rem;color:#fff;cursor:pointer;font-weight:600; }
        .btn-logout:hover { background:#fee2e2;border-color:#fca5a5;color:#dc2626; }
        .nav-role-badge { font-size:.7rem;background:#fef3c7;color:#92400e;border-radius:4px;padding:.1rem .35rem;margin-left:.3rem;font-weight:700; }
    </style>
</head>
<body>

    <header>
        <div class="header-inner">
            <div class="header-merk">
                <h1>Metropolis</h1>
            </div>

            <nav class="header-nav" aria-label="{{ __('Hoofdnavigatie') }}">
                <a href="{{ route('home') }}"
                   class="nav-link {{ request()->routeIs('home') ? 'is-actief' : '' }}">
                    {{ __('Stadsgebied') }}
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('effects.manager') }}"
                           class="nav-link {{ request()->routeIs('effects.manager') ? 'is-actief' : '' }}">
                            {{ __('Effecten beheren') }}
                        </a>
                    @endif
                @endauth
            </nav>

            <div style="display:flex;align-items:center;gap:1rem;">
                <div class="taal-knop">
                    <a href="{{ route('taal', 'nl') }}" class="taal-vlag {{ app()->getLocale() === 'nl' ? 'actief' : '' }}" title="Nederlands" aria-label="Nederlands">
                        <span class="fi fi-nl"></span>
                    </a>
                    <a href="{{ route('taal', 'en') }}" class="taal-vlag {{ app()->getLocale() === 'en' ? 'actief' : '' }}" title="English" aria-label="English">
                        <span class="fi fi-gb"></span>
                    </a>
                </div>
                @auth
                    <div class="user-info">
                        <span style="color:#d8e8d2;">{{ auth()->user()->name }}</span>
                        <span class="user-badge {{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-logout">{{ __('Uitloggen') }}</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="melding" role="status" aria-live="polite">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="melding melding-fout" role="alert" aria-live="assertive">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="melding melding-fout" role="alert" aria-live="assertive">
            <strong>{{ __('Er ging iets mis. Probeer het opnieuw.') }}</strong>
            <ul>
                @foreach($errors->all() as $fout)
                    <li>{{ $fout }}</li>
                @endforeach
            </ul>
        </div>
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
