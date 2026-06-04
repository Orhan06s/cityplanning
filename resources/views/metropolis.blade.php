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
            @endauth
        </h2>

        <form action="{{ url('/') }}" method="GET" class="grid-size-form">
            <label for="size">{{ __('Grid grootte (6 - 64):') }}</label>
            <input type="number" id="size" name="size" value="{{ $gridSize }}" min="6" max="64">
            <button type="submit" class="btn btn-small">{{ __('Toepassen') }}</button>
        </form>

        <p class="grid-help">Klik op een geplaatste cel om deze te selecteren. Klik een lege cel om de geselecteerde groep te verplaatsen. Klik met de rechtermuisknop een andere bezette cel om de geselecteerde cel ermee te koppelen.</p>
        @include('partials.grid')
    </div>

    @include('partials.bestemmingen')

</div>
@endsection
