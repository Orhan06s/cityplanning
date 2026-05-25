<input type="text" id="zoekterm" placeholder="{{ __('Zoek functie...') }}" autocomplete="off" aria-label="{{ __('Zoek functie...') }}">

<div id="library-lijst" role="list">
    @foreach($destinationsByCategory as $category => $items)
        <p class="categorie-titel">{{ __($category) }}</p>
        @foreach($items as $item)
            <div class="library-item"
                 role="listitem"
                 draggable="true"
                 tabindex="0"
                 data-id="{{ $item->id }}"
                 data-name="{{ $item->getNaam() }}"
                 data-icon="{{ $item->icon }}"
                 @if($item->description) title="{{ $item->description }}" @endif
                 aria-label="{{ $item->getNaam() }} - {{ __('sleep naar een cel om te plaatsen') }}">
                @if(str_starts_with($item->icon, '/'))
                    <img src="{{ $item->icon }}" alt="" class="functie-icon-img" aria-hidden="true">
                @else
                    <span aria-hidden="true">{{ $item->icon }}</span>
                @endif
                {{ $item->getNaam() }}
            </div>
        @endforeach
    @endforeach

    <p id="geen-resultaten" style="display:none; color: gray;" role="status">{{ __('Geen functies gevonden.') }}</p>
</div>
