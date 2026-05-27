@extends('layouts.app')

@section('content')
<div style="max-width:480px;margin:2rem auto;">
    <h2>{{ __('Nieuwe gebruiker aanmaken') }}</h2>

    <div style="background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.08);padding:2rem;">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-weight:600;margin-bottom:.35rem;">{{ __('Naam') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:7px;font-size:1rem;box-sizing:border-box;">
                @error('name') <div style="color:#dc2626;font-size:.82rem;margin-top:.3rem;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-weight:600;margin-bottom:.35rem;">{{ __('E-mailadres') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:7px;font-size:1rem;box-sizing:border-box;">
                @error('email') <div style="color:#dc2626;font-size:.82rem;margin-top:.3rem;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-weight:600;margin-bottom:.35rem;">{{ __('Rol') }}</label>
                <select name="role" required
                        style="width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:7px;font-size:1rem;box-sizing:border-box;">
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
                @error('role') <div style="color:#dc2626;font-size:.82rem;margin-top:.3rem;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-weight:600;margin-bottom:.35rem;">{{ __('Wachtwoord') }}</label>
                <input type="password" name="password" required autocomplete="new-password"
                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:7px;font-size:1rem;box-sizing:border-box;">
                @error('password') <div style="color:#dc2626;font-size:.82rem;margin-top:.3rem;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-weight:600;margin-bottom:.35rem;">{{ __('Wachtwoord bevestigen') }}</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:7px;font-size:1rem;box-sizing:border-box;">
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit"
                        style="background:#2563eb;color:#fff;border:none;border-radius:7px;padding:.65rem 1.25rem;font-size:1rem;font-weight:600;cursor:pointer;">
                    {{ __('Aanmaken') }}
                </button>
                <a href="{{ route('admin.users') }}"
                   style="background:#e5e7eb;color:#374151;border-radius:7px;padding:.65rem 1.25rem;font-size:1rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;">
                    {{ __('Annuleren') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
