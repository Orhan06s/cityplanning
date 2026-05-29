@extends('layouts.app')

@section('content')
<div class="gebruikers-pagina">
    <div class="gebruikers-header">
        <h2>{{ __('Gebruikersbeheer') }}</h2>
        <a href="{{ route('admin.users.create') }}" class="btn-blauw">
            + {{ __('Nieuwe gebruiker') }}
        </a>
    </div>

    <div class="gebruikers-tabel-wrapper">
        <table class="gebruikers-tabel">
            <thead>
                <tr>
                    <th>{{ __('Naam') }}</th>
                    <th>{{ __('E-mail') }}</th>
                    <th>{{ __('Rol') }}</th>
                    <th>{{ __('Acties') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="{{ $user->id === auth()->id() ? 'is-actief-gebruiker' : '' }}">
                        <td class="gebruiker-naam">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="gebruiker-jij">(jij)</span>
                            @endif
                        </td>
                        <td class="gebruiker-email">{{ $user->email }}</td>
                        <td>
                            <span class="rol-badge {{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                                      onsubmit="return confirm('{{ __('Weet je zeker dat je deze gebruiker wilt verwijderen?') }}')">
                                    @csrf
                                    <button type="submit" class="btn-verwijder">
                                        {{ __('Verwijderen') }}
                                    </button>
                                </form>
                            @else
                                <span class="gebruikers-geen-actie">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="gebruikers-geen">
                            {{ __('Geen gebruikers gevonden.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="gebruikers-legenda">
        💡 <strong>Admin</strong>: {{ __('volledig beheer') }} &nbsp;|&nbsp;
        <strong>Gebruiker</strong>: {{ __('grid bewerken') }} &nbsp;|&nbsp;
        <strong>Viewer</strong>: {{ __('alleen bekijken') }}
    </p>
</div>
@endsection
