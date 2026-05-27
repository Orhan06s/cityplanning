<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
        }
        .auth-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .auth-logo {
            text-align: center;
            padding: 2rem 2rem 0;
        }
        .auth-logo h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin: 0 0 .25rem;
        }
        .auth-logo p {
            color: #64748b;
            margin: 0 0 1.5rem;
            font-size: .9rem;
        }
        /* Tabs */
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
        }
        .auth-tab {
            flex: 1;
            padding: .75rem;
            text-align: center;
            font-size: .95rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border: none;
            background: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all .15s;
            text-decoration: none;
        }
        .auth-tab.actief {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }
        .auth-tab:hover:not(.actief) {
            color: #374151;
            background: #f9fafb;
        }
        /* Form */
        .auth-form {
            padding: 1.75rem 2rem 2rem;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: .35rem;
            color: #374151;
            font-size: .9rem;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: .6rem .85rem;
            border: 1.5px solid #d1d5db;
            border-radius: 7px;
            font-size: 1rem;
            transition: border-color .15s;
            box-sizing: border-box;
            background: #fff;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .form-error {
            color: #dc2626;
            font-size: .82rem;
            margin-top: .3rem;
        }
        .btn-primary {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: .75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: .5rem;
            transition: background .15s;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .remember-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1rem;
            font-size: .9rem;
            color: #374151;
        }
        .melding-fout-auth {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            border-radius: 7px;
            padding: .65rem .9rem;
            margin-bottom: 1rem;
            font-size: .88rem;
        }
        .rol-uitleg {
            font-size: .8rem;
            color: #6b7280;
            margin-top: .3rem;
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <h1>🏙️ Metropolis</h1>
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
            <div style="padding:1rem 2rem 0;">
                <div class="melding-fout-auth">
                    @foreach ($errors->all() as $fout)
                        <div>{{ $fout }}</div>
                    @endforeach
                </div>
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
