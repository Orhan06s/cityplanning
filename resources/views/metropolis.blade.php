@extends('layouts.app')

@section('content')

<script>
    window.metroEffectsMatrix = @json($effectsMatrix);
    window.metroGridCols = 4;
</script>

@include('partials.effects-menu')

@include('partials.totalen')

<div class="container">

    <div class="library">
        <h2>{{ __('Functiebibliotheek') }}</h2>
        @include('partials.library')
    </div>

    <div class="grid-wrapper">
        <h2>{{ __('Stadsgebied') }}
            <button id="clear-grid-btn" class="btn btn-small" style="margin-left:12px">{{ __('Leeg grid') }}</button>
        </h2>
        <p class="grid-help">Klik op een geplaatste cel om deze te selecteren. Klik een lege cel om de geselecteerde groep te verplaatsen. Klik met de rechtermuisknop een andere bezette cel om de geselecteerde cel ermee te koppelen.</p>
        @include('partials.grid')
    </div>

</div>
@endsection
