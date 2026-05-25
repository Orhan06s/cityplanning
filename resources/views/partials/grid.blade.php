<div class="grid" role="grid" aria-label="{{ __('Stadsgebied grid') }}">
    @foreach($gridCells as $cell)
        <div class="cel {{ $cell->destination ? 'bezet' : 'leeg' }}"
             role="gridcell"
             tabindex="0"
             data-cell-id="{{ $cell->id }}"
             data-position="{{ $cell->position }}"
             @if($cell->destination)
                 data-function-name="{{ $cell->destination->name }}"
                 data-category="{{ $cell->destination->category }}"
             @endif
             aria-label="Cel {{ $cell->position + 1 }}: {{ $cell->destination ? $cell->destination->getNaam() : __('leeg') }}">

            @if($cell->destination)
                <span class="cel-icon" aria-hidden="true">{{ $cell->destination->icon }}</span>
                <span class="cel-naam">{{ $cell->destination->getNaam() }}</span>

                <form action="{{ route('remove') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cell_id" value="{{ $cell->id }}">
                    <button type="submit" class="verwijder-btn"
                            aria-label="{{ __('Verwijder') }} {{ $cell->destination->getNaam() }}">✕</button>
                </form>
            @else
                <span class="leeg-tekst" aria-hidden="true">{{ __('Sleep hier') }}</span>
            @endif

        </div>
    @endforeach
</div>
