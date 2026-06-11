@extends('layouts.app')

@section('title', __('Stadsgebied') . ' — Metropolis')

@section('content')

<script>
    window.metroEffectsMatrix = @json($effectsMatrix);
    window.metroGridCols = Math.ceil(Math.sqrt({{ $gridSize }}));
    window.metroGridSize = {{ $gridSize }};
    window.metroCanEdit = {{ auth()->user()->canEdit() ? 'true' : 'false' }};
</script>

@include('partials.effects-menu')

@include('partials.totalen')

@auth
    @if(auth()->user()->isViewer())
        <div class="viewer-banner">
            {{ __('Je bekijkt dit als Viewer — je kunt niets aanpassen.') }}
        </div>
    @endif
@endauth

<div class="container">

    @auth
        @if(auth()->user()->canEdit())
            <div class="library">
                <h2>{{ __('Functiebibliotheek') }}</h2>
                @include('partials.library')
            </div>
        @endif
    @endauth

    <div class="grid-wrapper">
        <h2>{{ __('Stadsgebied') }}
            @auth
                @if(auth()->user()->canEdit())
                    <button id="clear-grid-btn" class="btn btn-small" style="margin-left:12px">{{ __('Leeg grid') }}</button>
                @endif
                @if(in_array(auth()->user()->role, ['gebruiker', 'admin']))
                    <a href="{{ route('export.pdf') }}" target="_blank" rel="noopener" class="btn btn-small" style="margin-left:8px; text-decoration:none; display:inline-block;">
                        <span aria-hidden="true">📄</span> {{ __('Exporteer PDF') }}
                    </a>
                @endif
            @endauth
        </h2>

        @include('partials.grid')
    </div>

    @include('partials.bestemmingen')

</div>
@endsection
