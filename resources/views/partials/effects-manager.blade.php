{{-- Trigger knop --}}
<div id="effects-manager-wrapper">
    <button
        id="effects-manager-trigger"
        class="effects-manager-btn"
        aria-expanded="false"
        aria-controls="effects-manager-panel"
        aria-label="{{ __('Effecten beheren') }}"
        type="button"
    >
        <span class="trigger-icon" aria-hidden="true">⚙️</span>
        <span class="trigger-label">{{ __('Effecten beheren') }}</span>
    </button>
</div>

{{-- Backdrop overlay --}}
<div id="effects-manager-backdrop" class="effects-manager-backdrop" hidden></div>

{{-- Hoofd modal --}}
<div
    id="effects-manager-panel"
    class="effects-manager-panel"
    role="dialog"
    aria-labelledby="effects-manager-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="effects-manager-inner">
        <div class="effects-manager-header">
            <h2 id="effects-manager-titel" class="effects-manager-titel">
                <span aria-hidden="true">⚙️</span>
                {{ __('Effecten beheren') }}
            </h2>
            <button
                id="effects-manager-sluit"
                class="effects-manager-sluit-btn"
                aria-label="{{ __('Sluit effecten beheer') }}"
                type="button"
            >&times;</button>
        </div>

        <p class="effects-manager-subtitel">
            {{ __('Pas hier de effect-waarden aan voor elke stadsfunctie per categorie.') }}
            <span class="effects-manager-hint">{{ __('Wijzigingen worden automatisch opgeslagen.') }}</span>
        </p>

        <div class="effects-manager-toolbar">
            <button
                id="add-function-btn"
                class="btn btn-add"
                type="button"
                aria-label="{{ __('Voeg nieuwe functie toe') }}"
            >
                <span aria-hidden="true">+</span> {{ __('Functie toevoegen') }}
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

            <span id="effects-manager-stats" class="effects-manager-stats" aria-live="polite"></span>
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
                                        <span class="effect-status" aria-hidden="true"></span>
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
                                >🗑️</button>
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
</div>

{{-- Submodal: nieuwe functie toevoegen --}}
<div
    id="add-function-modal"
    class="submodal"
    role="dialog"
    aria-labelledby="add-function-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="submodal-inner">
        <h3 id="add-function-titel" class="submodal-titel">{{ __('Nieuwe functie toevoegen') }}</h3>
        <p class="submodal-tekst">{{ __('Voer de naam van de nieuwe stadsfunctie in. Alle effect-waarden beginnen op 0.') }}</p>

        <form id="add-function-form" novalidate>
            <label for="add-function-input" class="submodal-label">{{ __('Functienaam') }}</label>
            <input
                type="text"
                id="add-function-input"
                class="submodal-input"
                maxlength="50"
                required
                autocomplete="off"
                aria-describedby="add-function-fout"
            />
            <p id="add-function-fout" class="submodal-fout" role="alert" hidden></p>

            <div class="submodal-knoppen">
                <button type="button" class="btn btn-cancel" data-close-submodal>{{ __('Annuleer') }}</button>
                <button type="submit" class="btn btn-add">{{ __('Toevoegen') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Submodal: verwijder bevestiging --}}
<div
    id="delete-function-modal"
    class="submodal"
    role="alertdialog"
    aria-labelledby="delete-function-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="submodal-inner">
        <h3 id="delete-function-titel" class="submodal-titel">{{ __('Functie verwijderen?') }}</h3>
        <p class="submodal-tekst">
            {{ __('Weet je zeker dat je deze functie wilt verwijderen?') }}
            <strong id="delete-function-naam"></strong>
        </p>

        <div class="submodal-knoppen">
            <button type="button" class="btn btn-cancel" data-close-submodal>{{ __('Annuleer') }}</button>
            <button type="button" id="delete-function-bevestig" class="btn btn-danger">{{ __('Verwijder') }}</button>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
