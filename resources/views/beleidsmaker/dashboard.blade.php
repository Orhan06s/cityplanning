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

    </div>
@endsection
