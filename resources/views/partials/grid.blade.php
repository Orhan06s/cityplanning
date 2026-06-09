@php
    // Aantal kolommen = afgeronde wortel van grid-grootte (12 → 4 cols, 16 → 4, 25 → 5)
    $COLUMNS = (int) ceil(sqrt($gridSize));
@endphp

{{-- Screenreader-aankondigingen voor grid-acties --}}
<div id="grid-aankondiging" class="sr-only" aria-live="polite" aria-atomic="true"></div>

<div class="grid"
     role="grid"
     aria-label="{{ __('Stadsgebied grid') }}"
     style="grid-template-columns: repeat({{ $COLUMNS }}, 1fr); grid-template-rows: none;">

    @foreach($gridCells as $cell)
        @php
            $row = intdiv($cell->position, $COLUMNS) + 1;
            $col = ($cell->position % $COLUMNS) + 1;
        @endphp
        <div class="cel {{ $cell->destination ? 'bezet' : 'leeg' }} {{ $cell->linked_group_id ? 'linked' : '' }} {{ $cell->is_destination ? 'is-bestemming' : '' }} {{ $cell->isApproved() ? 'is-goedgekeurd' : '' }}"
             role="gridcell"
             tabindex="0"
             data-cell-id="{{ $cell->id }}"
             data-position="{{ $cell->position }}"
             data-row="{{ $row }}"
             data-col="{{ $col }}"
             data-linked-group="{{ $cell->linked_group_id ?? '' }}"
             data-is-destination="{{ $cell->is_destination ? '1' : '0' }}"
             @if($cell->destination)
                 data-function-name="{{ $cell->destination->name }}"
                 data-category="{{ $cell->destination->category }}"
             @endif
             aria-label="Cel {{ $cell->position + 1 }}: {{ $cell->destination ? $cell->destination->getNaam() : __('leeg') }}{{ $cell->is_destination ? ' (' . __('bestemming') . ')' : '' }}"">

            @if($cell->destination)
                @if(str_starts_with($cell->destination->icon, '/'))
                    <img src="{{ $cell->destination->icon }}" alt="" class="cel-icon functie-icon-img" aria-hidden="true">
                @else
                    <span class="cel-icon" aria-hidden="true">{{ $cell->destination->icon }}</span>
                @endif
                <span class="cel-naam">{{ $cell->destination->getNaam() }}</span>

                @if($cell->linked_group_id)
                    <span class="cel-link-badge" aria-hidden="true">🔗</span>
                @endif

                @if($cell->is_destination)
                    <span class="cel-bestemming-badge" aria-label="{{ __('bestemming') }}" title="{{ __('Ingesteld als bestemming') }}">📍</span>
                @endif

                @if($cell->isApproved())
                    <span class="cel-goedgekeurd-badge"
                          aria-label="{{ __('Goedgekeurd') }}"
                          title="{{ __('Goedgekeurd door') }} {{ $cell->approver?->name ?? __('onbekend') }} {{ __('op') }} {{ $cell->approved_at?->format('d-m-Y H:i') }}">✓</span>
                @endif

                {{-- Acties: zichtbaar als de cel geselecteerd is --}}
                <div class="cel-acties" aria-hidden="true">
                    @if(!$cell->is_destination)
                        <button class="btn-stel-bestemming"
                                data-cell-id="{{ $cell->id }}"
                                type="button"
                                aria-label="{{ __('Stel in als bestemming') }}">
                            📍 {{ __('Bestemming') }}
                        </button>
                    @else
                        <button class="btn-verwijder-bestemming"
                                data-cell-id="{{ $cell->id }}"
                                type="button"
                                aria-label="{{ __('Verwijder als bestemming') }}">
                            ✕ {{ __('Bestemming') }}
                        </button>
                    @endif

                    {{-- REV.3.1 — Goedkeuren, alleen voor de gemeente (admin) --}}
                    @auth
                        @if(auth()->user()->isAdmin())
                            @if(!$cell->isApproved())
                                <button class="btn-goedkeuren"
                                        data-cell-id="{{ $cell->id }}"
                                        type="button"
                                        aria-label="{{ __('Keur deze bestemming goed') }}">
                                    ✓ {{ __('Goedkeuren') }}
                                </button>
                            @else
                                <span class="cel-goedkeur-status" role="status">
                                    ✓ {{ __('Goedgekeurd door') }} {{ $cell->approver?->name ?? __('onbekend') }}
                                    {{ __('op') }} {{ $cell->approved_at?->format('d-m-Y H:i') }}
                                </span>
                                <button class="btn-goedkeuring-intrekken"
                                        data-cell-id="{{ $cell->id }}"
                                        type="button"
                                        aria-label="{{ __('Goedkeuring intrekken') }}">
                                    ✕ {{ __('Intrekken') }}
                                </button>
                            @endif
                        @endif
                    @endauth
                </div>

                <form action="{{ route('remove') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cell_id" value="{{ $cell->id }}">
                    <input type="hidden" name="size" value="{{ $gridSize }}">
                    <button type="submit" class="verwijder-btn"
                            aria-label="{{ __('Verwijder') }} {{ $cell->destination->getNaam() }}">✕</button>
                </form>
            @else
                <span class="leeg-tekst" aria-hidden="true">{{ __('Sleep hier') }}</span>
            @endif

            <span class="cel-coords" aria-hidden="true">{{ $col }},{{ $row }}</span>

        </div>
    @endforeach
</div>
