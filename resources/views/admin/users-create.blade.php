@extends('layouts.app')

@section('content')
<div class="gebruikers-pagina">
    <div class="gebruikers-header">
        <h2>{{ __('Nieuwe gebruiker aanmaken') }}</h2>
        <a href="{{ route('admin.users') }}" class="btn-blauw">← {{ __('Terug') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="auth-form" style="max-width:480px;">
        @csrf

        <div class="form-group">
            <label for="name">{{ __('Naam') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('E-mailadres') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="role">{{ __('Rol') }}</label>
            <select id="role" name="role" required>
                <option value="admin"     {{ old('role') === 'admin'     ? 'selected' : '' }}>{{ __('Admin (volledig beheer)') }}</option>
                <option value="gebruiker" {{ old('role') === 'gebruiker' ? 'selected' : '' }}>{{ __('Gebruiker (grid bewerken)') }}</option>
                <option value="viewer"    {{ old('role') === 'viewer'    ? 'selected' : '' }}>{{ __('Viewer (alleen bekijken)') }}</option>
            </select>
            @error('role') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('Wachtwoord') }}</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{ __('Wachtwoord bevestigen') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn-primary">{{ __('Aanmaken') }}</button>
    </form>
</div>
@endsection
