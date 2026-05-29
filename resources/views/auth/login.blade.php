<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <h1>Metropolis</h1>
            <p>Metropolis van de Toekomst</p>
        </div>

        @if ($errors->any())
            <div class="melding-fout-auth">
                @foreach ($errors->all() as $fout)
                    <div>{{ $fout }}</div>
                @endforeach
            </div>
        @endif

        <div class="auth-form">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('E-mailadres') }}</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="jou@voorbeeld.nl">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Wachtwoord') }}</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="remember-row">
                    <input id="remember" type="checkbox" name="remember">
                    <label for="remember">{{ __('Onthoud mij') }}</label>
                </div>
                <button type="submit" class="btn-primary">{{ __('Inloggen') }}</button>
            </form>
        </div>

    </div>
</div>
</body>
</html>
