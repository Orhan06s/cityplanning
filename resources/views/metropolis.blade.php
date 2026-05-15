@extends('layouts.app')

@section('content')

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
