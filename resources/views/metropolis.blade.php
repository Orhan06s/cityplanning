@extends('layouts.app')

@section('content')

<script>
    window.metroEffectsMatrix = @json($effectsMatrix);
    window.metroGridCols = 4;
</script>

@include('partials.totalen')

<div class="container">

    <div class="library">
        <h2>{{ __('Functiebibliotheek') }}</h2>
        @include('partials.library')
        @include('partials.effects')
    </div>

    <div class="grid-wrapper">
        <h2>{{ __('Stadsgebied') }}</h2>
        @include('partials.grid')
    </div>

</div>
@endsection
