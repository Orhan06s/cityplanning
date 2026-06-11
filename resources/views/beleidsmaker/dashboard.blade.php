@extends('layouts.app')

@section('title', __('Beleidsmaker Dashboard'))

@section('content')
    <div class="dashboard-beleidsmaker" role="main" aria-labelledby="dashboard-titel">

        <header class="dashboard-header">
            <h2 id="dashboard-titel">
                <span aria-hidden="true">🏛️</span>
                {{ __('Welkom, :naam', ['naam' => auth()->user()->name]) }}
            </h2>
            <p class="dashboard-intro">
                {{ __('Dit is uw persoonlijke beleidsmaker-omgeving. Hier kunt u de stedelijke ontwikkeling volgen en beleidsinformatie inzien.') }}
            </p>
        </header>

        <section class="dashboard-sectie" aria-labelledby="overzicht-titel">
            <h3 id="overzicht-titel">
                <span aria-hidden="true">📋</span>
                {{ __('Stedelijk overzicht') }}
            </h3>
            <p>{{ __('Als beleidsmaker kunt u het stadsgebied bekijken en de effecten van stedelijke functies inzien.') }}</p>

            <a href="{{ route('home') }}"
               class="btn btn-primair"
               aria-label="{{ __('Bekijk het stadsgebied op de kaart') }}">
                <span aria-hidden="true">🗺️</span>
                {{ __('Bekijk stadsgebied') }}
            </a>
        </section>

    </div>
@endsection
