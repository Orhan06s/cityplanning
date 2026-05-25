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


<div
    id="add-function-modal"
    class="modal-overlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="add-function-modal-titel"
    aria-hidden="true"
    hidden
>
    <div class="modal-venster" role="document">
        <div class="modal-header">
            <h3 id="add-function-modal-titel" class="modal-titel">{{ __('Nieuwe functie toevoegen') }}</h3>
            <button
                type="button"
                class="modal-sluit-btn"
                id="add-function-modal-sluit"
                aria-label="{{ __('Sluiten') }}"
            >&times;</button>
        </div>

        <form id="add-function-form" class="modal-form" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="modal-fout" id="add-function-fout" role="alert" aria-live="assertive" hidden></div>

            <div class="form-veld">
                <label for="af-function-name">{{ __('Functienaam') }} <span class="verplicht" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="af-function-name"
                    name="function_name"
                    maxlength="50"
                    required
                    autocomplete="off"
                    aria-required="true"
                >
            </div>

            <div class="form-veld">
                <label for="af-category">{{ __('Categorie') }} <span class="verplicht" aria-hidden="true">*</span></label>
                <select id="af-category" name="category" required aria-required="true">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ __($cat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-veld">
                <label for="af-description">{{ __('Beschrijving') }}</label>
                <textarea
                    id="af-description"
                    name="description"
                    maxlength="255"
                    rows="2"
                    placeholder="{{ __('Optioneel') }}"
                ></textarea>
            </div>

            <fieldset class="form-veld form-icon">
                <legend>{{ __('Icoon') }}</legend>

                <div class="icon-preview-rij">
                    <span class="icon-preview" id="af-icon-preview" aria-live="polite">{{ $categoryIcons[$categories[0]] ?? '📍' }}</span>
                    <span class="icon-preview-label">{{ __('Voorbeeld') }}</span>
                </div>

                <div class="icon-preset-grid" role="radiogroup" aria-label="{{ __('Kies een icoon') }}">
                    @foreach($presetIcons as $catKey => $iconList)
                        @foreach($iconList as $emoji)
                            <button
                                type="button"
                                class="icon-keuze-btn"
                                data-icon="{{ $emoji }}"
                                aria-label="{{ $emoji }}"
                                role="radio"
                                aria-checked="false"
                            >{{ $emoji }}</button>
                        @endforeach
                    @endforeach
                </div>

                <input type="hidden" name="icon" id="af-icon" value="">

                <div class="form-upload">
                    <label for="af-icon-file" class="form-upload-label">
                        {{ __('Of upload je eigen icoon (PNG, JPG, SVG — max 2MB)') }}
                    </label>
                    <input
                        type="file"
                        id="af-icon-file"
                        name="icon_file"
                        accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml"
                    >
                </div>
            </fieldset>

            <fieldset class="form-veld form-effects">
                <legend>{{ __('Effect-punten per categorie') }}</legend>

                <div class="form-effects-grid">
                    @foreach($categories as $cat)
                        <div class="form-effect-veld">
                            <label for="af-effect-{{ $cat }}">{{ __($cat) }}</label>
                            <input
                                type="number"
                                id="af-effect-{{ $cat }}"
                                name="effects[{{ $cat }}]"
                                min="-5"
                                max="5"
                                step="1"
                                value="0"
                            >
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <div class="modal-acties">
                <button type="button" class="btn btn-annuleer" id="add-function-annuleer">{{ __('Annuleer') }}</button>
                <button type="submit" class="btn btn-add" id="add-function-submit">{{ __('Toevoegen') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.metroCategoryIcons = @json($categoryIcons);
</script>

@endsection
