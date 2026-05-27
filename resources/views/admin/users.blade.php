@extends('layouts.app')

@section('content')
<div style="max-width:700px;margin:2rem auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
        <h2 style="margin:0;">{{ __('Gebruikersbeheer') }}</h2>
        <a href="{{ route('register') }}"
           style="background:#2563eb;color:#fff;border-radius:7px;padding:.55rem 1rem;font-size:.9rem;font-weight:600;text-decoration:none;">
            + {{ __('Nieuwe gebruiker') }}
        </a>
    </div>

    <div style="background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.85rem;color:#6b7280;font-weight:600;">{{ __('Naam') }}</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.85rem;color:#6b7280;font-weight:600;">{{ __('E-mail') }}</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.85rem;color:#6b7280;font-weight:600;">{{ __('Rol') }}</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.85rem;color:#6b7280;font-weight:600;">{{ __('Acties') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom:1px solid #f1f5f9;{{ $user->id === auth()->id() ? 'background:#eff6ff;' : '' }}">
                        <td style="padding:.75rem 1rem;font-weight:600;">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span style="font-size:.75rem;color:#3b82f6;margin-left:.3rem;">(jij)</span>
                            @endif
                        </td>
                        <td style="padding:.75rem 1rem;color:#374151;">{{ $user->email }}</td>
                        <td style="padding:.75rem 1rem;">
                            @if($user->role === 'admin')
                                <span style="background:#dbeafe;color:#1d4ed8;border-radius:999px;padding:.2rem .7rem;font-size:.8rem;font-weight:600;">Admin</span>
                            @elseif($user->role === 'gebruiker')
                                <span style="background:#fef3c7;color:#92400e;border-radius:999px;padding:.2rem .7rem;font-size:.8rem;font-weight:600;">Gebruiker</span>
                            @else
                                <span style="background:#f0fdf4;color:#166534;border-radius:999px;padding:.2rem .7rem;font-size:.8rem;font-weight:600;">Viewer</span>
                            @endif
                        </td>
                        <td style="padding:.75rem 1rem;">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                                      onsubmit="return confirm('{{ __('Weet je zeker dat je deze gebruiker wilt verwijderen?') }}')">
                                    @csrf
                                    <button type="submit"
                                            style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:.35rem .75rem;font-size:.82rem;font-weight:600;cursor:pointer;">
                                        {{ __('Verwijderen') }}
                                    </button>
                                </form>
                            @else
                                <span style="color:#9ca3af;font-size:.82rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:1.5rem;text-align:center;color:#9ca3af;">
                            {{ __('Geen gebruikers gevonden.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="margin-top:1rem;font-size:.85rem;color:#6b7280;">
        💡 <strong>Admin</strong>: {{ __('volledig beheer') }} &nbsp;|&nbsp;
        <strong>Gebruiker</strong>: {{ __('grid bewerken') }} &nbsp;|&nbsp;
        <strong>Viewer</strong>: {{ __('alleen bekijken') }}
    </p>
</div>
@endsection
