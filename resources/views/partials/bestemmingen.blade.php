<aside class="bestemmingen-panel" aria-label="{{ __('Bestemmingen in de stad') }}">

    <div class="bestemmingen-header">
        <h2 class="bestemmingen-titel">{{ __('Bestemmingen') }}</h2>
        <span class="bestemmingen-count" aria-live="polite">{{ $bestemmingen->count() }}</span>
    </div>

    {{-- Route maker --}}
    @if(!$bestemmingen->isEmpty())
        <div class="route-maker">
            <button id="route-start-btn" type="button" class="route-knop">{{ __('Kies route') }}</button>
            <button id="route-wis-btn" type="button" class="route-wis-knop" hidden>{{ __('Wis route') }}</button>
            <p class="route-info" id="route-info" aria-live="polite"></p>
        </div>
    @endif

    @if($bestemmingen->isEmpty())
        <div class="bestemmingen-leeg-staat">
            <span class="bestemmingen-leeg-icoon" aria-hidden="true">📍</span>
            <p>{{ __('Nog geen bestemmingen.') }}</p>
            <p class="bestemmingen-leeg-hint">{{ __('Klik op een functie om hem als bestemming te kiezen.') }}</p>
        </div>
    @else
        <ul class="bestemmingen-lijst" role="list">
            @foreach($bestemmingen as $cel)
                <li class="bestemmingen-item" role="listitem">
                    <div class="bestemmingen-item-info">
                        @if($cel->destination)
                            <span class="bestemmingen-item-icon" aria-hidden="true">
                                @if(str_starts_with($cel->destination->icon, '/'))
                                    <img src="{{ $cel->destination->icon }}" alt="" class="bestemmingen-img-icon">
                                @else
                                    {{ $cel->destination->icon }}
                                @endif
                            </span>
                            <div class="bestemmingen-item-tekst">
                                <span class="bestemmingen-item-naam">{{ $cel->destination->getNaam() }}</span>
                                <span class="bestemmingen-item-cat bestemmingen-cat-{{ $cel->destination->category }}">{{ __($cel->destination->category) }}</span>
                            </div>
                        @else
                            <span class="bestemmingen-item-icon" aria-hidden="true">📍</span>
                            <div class="bestemmingen-item-tekst">
                                <span class="bestemmingen-item-naam bestemmingen-leeg-cel">{{ __('Lege plek') }}</span>
                                <span class="bestemmingen-item-cel">{{ __('Cel') }} {{ $cel->position + 1 }}</span>
                            </div>
                        @endif
                    </div>
                    <button class="bestemmingen-item-verwijder btn-verwijder-bestemming"
                            data-cell-id="{{ $cel->id }}"
                            type="button"
                            aria-label="{{ __('Verwijder als bestemming') }}">✕</button>
                </li>
            @endforeach
        </ul>
    @endif

</aside>
