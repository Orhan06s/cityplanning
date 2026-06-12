@extends('layouts.app')

@section('title', __('Beleidsmaker Dashboard'))

@section('content')
    <div class="dashboard-beleidsmaker" role="main" aria-labelledby="dashboard-titel">

        <header class="dashboard-header">
            <h2 id="dashboard-titel">
                {{ __('Hallo, :naam', ['naam' => auth()->user()->name]) }}
            </h2>
            <p class="dashboard-intro">
                {{ __('Bekijk het stadsgebied, geef feedback en keur bestemmingen goed.') }}
            </p>
        </header>

        <section class="dashboard-sectie" style="text-align:center; margin-top:2rem;">
            <a href="{{ route('home') }}"
               class="btn btn-primair"
               style="font-size:1.1rem; padding:14px 28px;"
               aria-label="{{ __('Bekijk het stadsgebied') }}">
                <span aria-hidden="true">🗺️</span>
                {{ __('Bekijk stadsgebied') }}
            </a>

            <a href="{{ route('beleidsmaker.feedback') }}"
               class="btn btn-primair"
               style="font-size:1.1rem; padding:14px 28px; margin-left:8px;"
               aria-label="{{ __('Geef feedback op het stadsgebied') }}">
                <span aria-hidden="true">💬</span>
                {{ __('Feedback geven') }}
            </a>
        </section>

    </div>
@endsection
