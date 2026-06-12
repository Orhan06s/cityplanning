@extends('layouts.app')

@section('title', __('Stadsgebied') . ' — Metropolis')

@section('content')




<script>
    window.metroEffectsMatrix = @json($effectsMatrix);
    window.metroDayNightModifiers = @json($dayNightModifiers);
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
<div class="sim-controls" role="toolbar" aria-label="{{ __('Simulatiebediening') }}">

    <span id="sim-phase-label" class="sim-phase">{{ __('Dag') }}</span>

    <span id="sim-clock" class="sim-clock" aria-label="{{ __('Simulatietijd') }}">00:00</span>

    <div class="sim-speed-groep" role="group" aria-label="{{ __('Simulatiesnelheid') }}">
        <button class="btn-speed is-actief" data-speed-key="slow"   type="button">{{ __('Realtime') }}</button>
        <button class="btn-speed"           data-speed-key="normal" type="button">{{ __('Normaal') }}</button>
        <button class="btn-speed"           data-speed-key="fast"   type="button">{{ __('Snel') }}</button>
    </div>

    <button id="sim-pause-btn" class="btn-sim-pause" type="button"
            aria-pressed="false"
            aria-label="{{ __('Simulatie pauzeren') }}">
        <span class="pause-text">{{ __('Pauze') }}</span>
        <span class="resume-text hidden">{{ __('Hervat') }}</span>
    </button>

</div>

<script>window.simLabels = { dag: "{{ __('Dag') }}", nacht: "{{ __('Nacht') }}", pauze: "{{ __('Pauze') }}", hervat: "{{ __('Hervat') }}" };</script>
        @include('partials.grid')
    </div>

    @include('partials.bestemmingen')

</div>
@endsection
