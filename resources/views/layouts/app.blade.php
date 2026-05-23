<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body>

    <header>
        <div class="header-inner">
            <div>
                <h1> Metropolis</h1>
            </div>
            <div class="taal-knop">
                <a href="{{ route('taal', 'nl') }}" class="taal-vlag {{ app()->getLocale() === 'nl' ? 'actief' : '' }}" title="Nederlands" aria-label="Nederlands">
                    <span class="fi fi-nl"></span>
                </a>
                <a href="{{ route('taal', 'en') }}" class="taal-vlag {{ app()->getLocale() === 'en' ? 'actief' : '' }}" title="English" aria-label="English">
                    <span class="fi fi-gb"></span>
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="melding" role="status" aria-live="polite">{{ session('success') }}</div>
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

    <script>
        window.metroI18n = {
            saved: @json(__('Opgeslagen')),
            saving: @json(__('Opslaan...')),
            saveError: @json(__('Fout bij opslaan!')),
            addError: @json(__('Fout bij toevoegen!')),
            deleteError: @json(__('Fout bij verwijderen!')),
            nameRequired: @json(__('Voer een naam in.')),
            nameExists: @json(__('Deze functie bestaat al.')),
            nameTooLong: @json(__('Naam is te lang (max 50 tekens).')),
            functionAdded: @json(__('Functie toegevoegd!')),
            functionDeleted: @json(__('Functie verwijderd!')),
            noResults: @json(__('Geen functies gevonden.')),
            functionCount: @json(__(':count functies')),
            delete: @json(__('Verwijder')),
        };
    </script>
    <script src="{{ asset('js/metropolis.js') }}?v={{ filemtime(public_path('js/metropolis.js')) }}"></script>
</body>
</html>
