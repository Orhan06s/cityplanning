@php
    // Aantal kolommen = afgeronde wortel van grid-grootte (12 → 4 cols, 16 → 4, 25 → 5)
    $COLUMNS = (int) ceil(sqrt($gridSize));
@endphp

<div class="grid"
     role="grid"
     aria-label="{{ __('Stadsgebied grid') }}"
     style="grid-template-columns: repeat({{ $COLUMNS }}, 1fr); grid-template-rows: none;">

    @foreach($gridCells as $cell)
        @php
            $row = intdiv($cell->position, $COLUMNS) + 1;
            $col = ($cell->position % $COLUMNS) + 1;
        @endphp
        <div class="cel {{ $cell->destination ? 'bezet' : 'leeg' }} {{ $cell->linked_group_id ? 'linked' : '' }}"
             role="gridcell"
             tabindex="0"
             data-cell-id="{{ $cell->id }}"
             data-position="{{ $cell->position }}"
             data-row="{{ $row }}"
             data-col="{{ $col }}"
             data-linked-group="{{ $cell->linked_group_id ?? '' }}"
             @if($cell->destination)
                 data-function-name="{{ $cell->destination->name }}"
                 data-category="{{ $cell->destination->category }}"
             @endif
             aria-label="Cel {{ $cell->position + 1 }}: {{ $cell->destination ? $cell->destination->getNaam() : __('leeg') }}">

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
