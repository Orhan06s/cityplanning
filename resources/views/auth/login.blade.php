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

        {{-- Tabs --}}
        @php $actieveTab = $tab ?? (session('_old_input.name') ? 'register' : 'login'); @endphp
        <div class="auth-tabs">
            <a href="{{ route('login') }}"
               class="auth-tab {{ $actieveTab === 'login' ? 'actief' : '' }}">
                {{ __('Inloggen') }}
            </a>
            <a href="{{ route('register') }}"
               class="auth-tab {{ $actieveTab === 'register' ? 'actief' : '' }}">
                {{ __('Account aanmaken') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="melding-fout-auth">
                @foreach ($errors->all() as $fout)
                    <div>{{ $fout }}</div>
                @endforeach
            </div>
        @endif

        {{-- Login formulier --}}
        @if($actieveTab === 'login')
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
        @endif

        {{-- Registratie formulier --}}
        @if($actieveTab === 'register')
        <div class="auth-form">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="reg_name">{{ __('Naam') }}</label>
                    <input id="reg_name" type="text" name="name"
                           value="{{ old('name') }}"
                           required autocomplete="name"
                           placeholder="Jan de Vries">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="reg_email">{{ __('E-mailadres') }}</label>
                    <input id="reg_email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="username"
                           placeholder="jou@voorbeeld.nl">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="reg_role">{{ __('Rol') }}</label>
                    <select id="reg_role" name="role" required>
                        <option value="gebruiker" {{ old('role') === 'gebruiker' ? 'selected' : '' }}>
                            {{ __('Gebruiker — grid bewerken') }}
                        </option>
                        <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>
                            {{ __('Viewer — alleen bekijken') }}
                        </option>
                    </select>
                    @error('role') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="reg_password">{{ __('Wachtwoord') }}</label>
                    <input id="reg_password" type="password" name="password"
                           required autocomplete="new-password"
                           placeholder="••••••••">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="reg_password_confirmation">{{ __('Wachtwoord bevestigen') }}</label>
                    <input id="reg_password_confirmation" type="password"
                           name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary">{{ __('Account aanmaken') }}</button>
            </form>
        </div>
        @endif

    </div>
</div>
</body>
</html>
