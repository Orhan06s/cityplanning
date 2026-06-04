@extends('layouts.app')

@section('title', __('Nieuwe gebruiker aanmaken') . ' — Metropolis')

@section('content')
<div class="register-pagina">
    <h2>{{ __('Nieuwe gebruiker aanmaken') }}</h2>

    <div class="register-card">
        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="reg-naam">{{ __('Naam') }}</label>
                <input type="text" id="reg-naam" name="name" value="{{ old('name') }}"
                       required autocomplete="name"
                       aria-describedby="{{ $errors->has('name') ? 'fout-naam' : '' }}">
                @error('name')
                    <div id="fout-naam" class="auth-fout" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="reg-email">{{ __('E-mailadres') }}</label>
                <input type="email" id="reg-email" name="email" value="{{ old('email') }}"
                       required autocomplete="email"
                       aria-describedby="{{ $errors->has('email') ? 'fout-email' : '' }}">
                @error('email')
                    <div id="fout-email" class="auth-fout" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="reg-rol">{{ __('Rol') }}</label>
                <select id="reg-rol" name="role" required
                        aria-describedby="{{ $errors->has('role') ? 'fout-rol' : '' }}">
                    <option value="gebruiker" {{ old('role') === 'gebruiker' ? 'selected' : '' }}>
                        {{ __('Gebruiker (grid bewerken)') }}
                    </option>
                    <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>
                        {{ __('Viewer (alleen bekijken)') }}
                    </option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                        {{ __('Admin (volledig beheer)') }}
                    </option>
                </select>
                @error('role')
                    <div id="fout-rol" class="auth-fout" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="reg-wachtwoord">{{ __('Wachtwoord') }}</label>
                <input type="password" id="reg-wachtwoord" name="password"
                       required autocomplete="new-password"
                       aria-describedby="{{ $errors->has('password') ? 'fout-wachtwoord' : '' }}">
                @error('password')
                    <div id="fout-wachtwoord" class="auth-fout" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="reg-wachtwoord-bevestig">{{ __('Wachtwoord bevestigen') }}</label>
                <input type="password" id="reg-wachtwoord-bevestig" name="password_confirmation"
                       required autocomplete="new-password">
            </div>

            <div class="auth-knoppen">
                <button type="submit" class="btn-blauw">{{ __('Aanmaken') }}</button>
                <a href="{{ route('admin.users') }}" class="btn-grijs">{{ __('Annuleren') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
