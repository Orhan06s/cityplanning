@extends('layouts.app')

@section('content')
<div class="register-pagina">
    <h2>{{ __('Nieuwe gebruiker aanmaken') }}</h2>

    <div class="register-card">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>{{ __('Naam') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="auth-fout">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>{{ __('E-mailadres') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="auth-fout">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>{{ __('Rol') }}</label>
                <select name="role" required>
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
                @error('role') <div class="auth-fout">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>{{ __('Wachtwoord') }}</label>
                <input type="password" name="password" required autocomplete="new-password">
                @error('password') <div class="auth-fout">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>{{ __('Wachtwoord bevestigen') }}</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="auth-knoppen">
                <button type="submit" class="btn-blauw">{{ __('Aanmaken') }}</button>
                <a href="{{ route('admin.users') }}" class="btn-grijs">{{ __('Annuleren') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
