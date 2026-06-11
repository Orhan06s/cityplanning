<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Metropolis')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body>

    {{-- Skip-to-content: screenreaders en toetsenbordgebruikers kunnen direct naar de inhoud springen --}}
    <a href="#main-content" class="skip-link">{{ __('Ga naar inhoud') }}</a>

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
                            {{ __('Functies & Effecten') }}
                        </a>
                        <a href="{{ route('admin.users') }}"
                           class="nav-link {{ request()->routeIs('admin.users*') ? 'is-actief' : '' }}">
                            {{ __('Gebruikers') }}
                        </a>
                    @endif
                    @if(auth()->user()->isBeleidsmaker())
                        <a href="{{ route('beleidsmaker.dashboard') }}"
                           class="nav-link {{ request()->routeIs('beleidsmaker.dashboard') ? 'is-actief' : '' }}">
                            {{ __('Dashboard') }}
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="header-rechts">
                {{-- Screenreader knop --}}
                <button id="sr-knop" class="sr-toggle-btn" aria-pressed="false"
                        aria-label="{{ __('Screenreader inschakelen') }}"
                        data-aan-label="{{ __('Voorlezen aan') }}"
                        data-uit-label="{{ __('Voorlezen') }}"
                        data-aan-aria="{{ __('Screenreader uitschakelen') }}"
                        data-uit-aria="{{ __('Screenreader inschakelen') }}"
                        type="button">
                    <span aria-hidden="true">👁</span>
                    <span class="sr-knop-label">{{ __('Voorlezen') }}</span>
                </button>

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
                        <span class="user-naam">{{ auth()->user()->name }}</span>
                        <span class="user-badge {{ auth()->user()->role }}">{{ __(auth()->user()->role) }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
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

    <main id="main-content">
        @yield('content')
    </main>

    <footer>
        <p>Metropolis Simulator &copy; {{ date('Y') }}</p>
    </footer>

    <script src="{{ asset('js/screenreader.js') }}?v={{ filemtime(public_path('js/screenreader.js')) }}"></script>
    <script src="{{ asset('js/metropolis.js') }}?v={{ filemtime(public_path('js/metropolis.js')) }}"></script>
</body>
</html>
