<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Inloggen') }} – Metropolis</title>
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
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-logo h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin: 0;
        }
        .auth-logo p {
            color: #64748b;
            margin: .25rem 0 0;
            font-size: .9rem;
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
        .form-group input {
            width: 100%;
            padding: .6rem .85rem;
            border: 1.5px solid #d1d5db;
            border-radius: 7px;
            font-size: 1rem;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .form-group input:focus {
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
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <h1> Metropolis</h1>
            <p>{{ __('Log in om verder te gaan') }}</p>
        </div>

        @if ($errors->any())
            <div class="melding-fout-auth">
                @foreach ($errors->all() as $fout)
                    <div>{{ $fout }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('E-mailadres') }}</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="jou@voorbeeld.nl">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Wachtwoord') }}</label>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input id="remember" type="checkbox" name="remember">
                <label for="remember">{{ __('Onthoud mij') }}</label>
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Inloggen') }}
            </button>
        </form>
    </div>
</div>
</body>
</html>
