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

<div
    id="effects-manager-panel"
    class="effects-manager-panel"
    role="dialog"
    aria-labelledby="effects-manager-titel"
    aria-hidden="true"
    hidden
>
    <div class="effects-manager-inner">
        <div class="effects-manager-header">
            <h2 id="effects-manager-titel" class="effects-manager-titel">{{ __('Effecten beheren') }}</h2>
            <button
                id="effects-manager-sluit"
                class="effects-manager-sluit-btn"
                aria-label="{{ __('Sluit effecten beheer') }}"
                type="button"
            >&times;</button>
        </div>

        <p class="effects-manager-subtitel">
            {{ __('Pas hier de effect-waarden aan voor elke stadsfunctie per categorie.') }}
        </p>

        <div class="effects-manager-actions">
            <button
                id="add-function-btn"
                class="btn btn-add"
                type="button"
                aria-label="{{ __('Voeg nieuwe functie toe') }}"
            >
                + {{ __('Functie toevoegen') }}
            </button>
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
                        <th scope="col" class="th-actions">{{ __('Acties') }}</th>
                    </tr>
                </thead>
                <tbody id="effects-tbody">
                    @foreach($groupedByFunction as $functionName => $effects)
                        <tr role="row" class="effects-row" data-function="{{ $functionName }}">
                            <td class="td-functienaam">{{ __($functionName) }}</td>
                            @foreach($categories as $category)
                                @php
                                    $effect = $effects->firstWhere('category', $category);
                                    $points = $effect?->points ?? 0;
                                @endphp
                                <td
                                    role="cell"
                                    class="td-points"
                                    data-function="{{ $functionName }}"
                                    data-category="{{ $category }}"
                                >
                                    <input
                                        type="number"
                                        class="effect-input"
                                        value="{{ $points }}"
                                        min="-5"
                                        max="5"
                                        data-function="{{ $functionName }}"
                                        data-category="{{ $category }}"
                                        aria-label="{{ __($functionName) }} - {{ __($category) }}"
                                    />
                                </td>
                            @endforeach
                            <td class="td-actions">
                                <button
                                    class="btn-delete-function"
                                    data-function="{{ $functionName }}"
                                    aria-label="{{ __('Verwijder') }} {{ __($functionName) }}"
                                    type="button"
                                >
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
