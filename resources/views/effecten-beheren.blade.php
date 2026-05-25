@extends('layouts.app')

@section('content')

<div class="manager-pagina">

    <div class="manager-pagina-header">
        <h2 class="manager-pagina-titel">{{ __('Effecten beheren') }}</h2>
    </div>

    <div class="effects-manager-toolbar">
        <button
            id="add-function-btn"
            class="btn btn-add"
            type="button"
            aria-label="{{ __('Voeg nieuwe functie toe') }}"
        >
            <span aria-hidden="true">+</span> {{ __('Functie toevoegen') }}
        </button>

        <button
            id="reset-defaults-btn"
            class="btn btn-reset"
            type="button"
            aria-label="{{ __('Reset alle effecten naar standaardwaarden') }}"
            title="{{ __('Herstel alle effecten naar de standaardwaarden uit config/effects.php') }}"
        >
            <span aria-hidden="true">↻</span> {{ __('Reset naar defaults') }}
        </button>

        <div class="effects-manager-zoek">
            <input
                type="text"
                id="effects-manager-zoek-input"
                placeholder="{{ __('Zoek functie...') }}"
                aria-label="{{ __('Zoek functie...') }}"
                autocomplete="off"
            >
        </div>
    </div>

    <div class="effects-manager-tabel-wrapper" tabindex="0">
        <table
            class="effects-manager-tabel"
            role="table"
            aria-label="{{ __('Editable effecten per stadsfunctie') }}"
        >
            <thead>
                <tr role="row">
                    <th scope="col" class="th-functie">{{ __('Functie') }}</th>
                    @foreach($categories as $category)
                        <th scope="col" class="th-categorie">{{ __($category) }}</th>
                    @endforeach
                    <th scope="col" class="th-actions" aria-label="{{ __('Acties') }}"></th>
                </tr>
            </thead>
            <tbody id="effects-tbody">
                @forelse($groupedByFunction as $functionName => $effects)
                    <tr role="row" class="effects-row" data-function="{{ $functionName }}">
                        <td class="td-functienaam">{{ __($functionName) }}</td>
                        @foreach($categories as $category)
                            @php
                                $effect = $effects->firstWhere('category', $category);
                                $points = $effect?->points ?? 0;
                            @endphp
                            <td role="cell" class="td-points">
                                <div class="effect-input-wrap" data-status="idle">
                                    <input
                                        type="number"
                                        class="effect-input"
                                        value="{{ $points }}"
                                        min="-5"
                                        max="5"
                                        step="1"
                                        data-function="{{ $functionName }}"
                                        data-category="{{ $category }}"
                                        data-initial="{{ $points }}"
                                        aria-label="{{ __($functionName) }} - {{ __($category) }}"
                                    />
                                </div>
                            </td>
                        @endforeach
                        <td class="td-actions">
                            <button
                                class="btn-delete-function"
                                data-function="{{ $functionName }}"
                                aria-label="{{ __('Verwijder') }} {{ __($functionName) }}"
                                type="button"
                                title="{{ __('Verwijder') }} {{ __($functionName) }}"
                            >{{ __('Verwijder') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr id="effects-empty-row">
                        <td colspan="{{ count($categories) + 2 }}" class="effects-empty">
                            {{ __('Nog geen functies. Voeg er een toe!') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
