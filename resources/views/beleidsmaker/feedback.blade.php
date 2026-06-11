@extends('layouts.app')

@section('title', __('Feedback geven') . ' — Metropolis')

@section('content')

<script>
    window.metroGridCols = {{ $gridCols }};
    window.metroGridSize = {{ $size }};
    window.metroCanEdit  = false;
</script>

<div class="container">

    <div class="grid-wrapper" style="position:relative;">
        <h2>{{ __('Stadsgebied — feedback geven') }}</h2>
        <p class="manager-pagina-tekst">{{ __('Klik op een cel om daar een opmerking of verbetering aan te koppelen. Of voeg een algemene opmerking toe.') }}</p>

        {{-- Grid (read-only, klikbaar voor feedback) --}}
        <div class="grid feedback-grid"
             role="grid"
             aria-label="{{ __('Stadsgebied grid') }}"
             style="grid-template-columns: repeat({{ $gridCols }}, 1fr);">

            @foreach($gridCells as $cell)
                @php
                    $row = intdiv($cell->position, $gridCols) + 1;
                    $col = ($cell->position % $gridCols) + 1;
                    $hasFeedback = $feedback->where('position', $cell->position)->isNotEmpty();
                @endphp
                <div class="cel {{ $cell->destination ? 'bezet' : 'leeg' }} feedback-cel {{ $hasFeedback ? 'heeft-feedback' : '' }}"
                     data-position="{{ $cell->position }}"
                     data-col="{{ $col }}"
                     data-row="{{ $row }}"
                     role="gridcell"
                     tabindex="0"
                     title="{{ __('Klik om feedback toe te voegen') }}"
                     aria-label="Cel {{ $cell->position + 1 }}: {{ $cell->destination ? $cell->destination->getNaam() : __('leeg') }}">

                    @if($cell->destination)
                        @if(str_starts_with($cell->destination->icon, '/'))
                            <img src="{{ $cell->destination->icon }}" alt="" class="cel-icon functie-icon-img" aria-hidden="true">
                        @else
                            <span class="cel-icon" aria-hidden="true">{{ $cell->destination->icon }}</span>
                        @endif
                        <span class="cel-naam">{{ $cell->destination->getNaam() }}</span>
                    @else
                        <span class="leeg-tekst" aria-hidden="true">{{ __('leeg') }}</span>
                    @endif

                    <span class="cel-coords" aria-hidden="true">{{ $col }},{{ $row }}</span>

                    @if($hasFeedback)
                        <span class="feedback-pin" aria-hidden="true">💬</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Feedbackformulier --}}
    <div class="feedback-formulier-wrapper">
        <div id="feedback-formulier" class="manager-pagina">
            <h3 id="feedback-formulier-titel">{{ __('Opmerking toevoegen') }}</h3>
            <p id="feedback-locatie-label" class="feedback-locatie-tekst"></p>

            <div class="form-veld">
                <label for="fb-type">{{ __('Type') }}</label>
                <select id="fb-type">
                    <option value="comment">💬 {{ __('Opmerking') }}</option>
                    <option value="improvement">💡 {{ __('Verbetering') }}</option>
                </select>
            </div>

            <div class="form-veld">
                <label for="fb-comment">{{ __('Tekst') }} <span class="verplicht" aria-hidden="true">*</span></label>
                <textarea id="fb-comment" rows="3" maxlength="1000" placeholder="{{ __('Typ hier uw opmerking...') }}"></textarea>
            </div>

            <div class="modal-acties">
                <button type="button" id="fb-annuleer" class="btn btn-annuleer">{{ __('Annuleer') }}</button>
                <button type="button" id="fb-opslaan" class="btn btn-add">{{ __('Opslaan') }}</button>
            </div>

            <div id="fb-fout" class="modal-fout" hidden></div>
        </div>

        {{-- Lijst van geplaatste feedback --}}
        <div class="manager-pagina" style="margin-top:2rem;">
            <h3>{{ __('Mijn opmerkingen') }}</h3>

            @if($submitted)
                <div class="viewer-banner" style="margin-bottom:1rem;">
                    {{ __('U heeft dit grid als') }}
                    <strong>{{ $submitted->status === 'approved' ? __('goedgekeurd') : __('afgekeurd') }}</strong>
                    {{ __('teruggestuurd naar de stedenbouwers.') }}
                </div>
            @endif

            <div id="feedback-lijst">
                @forelse($feedback as $item)
                    <div class="feedback-item {{ $item->type }}" data-id="{{ $item->id }}">
                        <span class="feedback-item-icon">{{ $item->type === 'improvement' ? '💡' : '💬' }}</span>
                        <div class="feedback-item-body">
                            <span class="feedback-item-locatie">
                                @if($item->position !== null)
                                    {{ __('Cel') }} {{ $item->position + 1 }}
                                @else
                                    {{ __('Algemeen') }}
                                @endif
                            </span>
                            <p class="feedback-item-tekst">{{ $item->comment }}</p>
                        </div>
                        <button type="button" class="btn-delete-function fb-verwijder-btn" data-id="{{ $item->id }}" aria-label="{{ __('Verwijder opmerking') }}">{{ __('Verwijderen') }}</button>
                    </div>
                @empty
                    <p id="feedback-leeg">{{ __('Nog geen opmerkingen toegevoegd.') }}</p>
                @endforelse
            </div>

            {{-- Verzenden --}}
            @if(!$submitted)
                <div class="feedback-verzenden" style="margin-top:1.5rem;">
                    <h4>{{ __('Simulatie beoordelen') }}</h4>
                    <div class="form-rij form-rij-2" style="margin-bottom:1rem;">
                        <button type="button" id="fb-goedkeuren" class="btn btn-add">✅ {{ __('Goedkeuren') }}</button>
                        <button type="button" id="fb-afkeuren" class="btn btn-danger">❌ {{ __('Afkeuren') }}</button>
                    </div>
                    <div id="fb-submit-fout" class="modal-fout" hidden></div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    var selectedPosition = null;

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify(data)
        }).then(function(r) { return r.json(); });
    }

    function deleteJson(url) {
        return fetch(url, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() }
        }).then(function(r) { return r.json(); });
    }

    // Klik op een cel → stel locatie in voor het formulier
    document.querySelectorAll('.feedback-cel').forEach(function(cel) {
        cel.addEventListener('click', function() {
            document.querySelectorAll('.feedback-cel.feedback-actief').forEach(function(c) {
                c.classList.remove('feedback-actief');
            });
            cel.classList.add('feedback-actief');
            selectedPosition = parseInt(cel.dataset.position, 10);
            var col = cel.dataset.col;
            var row = cel.dataset.row;
            document.getElementById('feedback-locatie-label').textContent = '📍 Cel ' + col + ',' + row;
            document.getElementById('fb-comment').focus();
        });
    });

    // Annuleer
    document.getElementById('fb-annuleer').addEventListener('click', function() {
        selectedPosition = null;
        document.querySelectorAll('.feedback-cel.feedback-actief').forEach(function(c) {
            c.classList.remove('feedback-actief');
        });
        document.getElementById('feedback-locatie-label').textContent = '';
        document.getElementById('fb-comment').value = '';
        document.getElementById('fb-fout').hidden = true;
    });

    // Opslaan
    document.getElementById('fb-opslaan').addEventListener('click', function() {
        var comment = document.getElementById('fb-comment').value.trim();
        var type    = document.getElementById('fb-type').value;
        var fout    = document.getElementById('fb-fout');

        if (!comment) {
            fout.textContent = '{{ __("Voer een opmerking in.") }}';
            fout.hidden = false;
            return;
        }
        fout.hidden = true;

        postJson('{{ route("beleidsmaker.feedback.store") }}', {
            comment:  comment,
            type:     type,
            position: selectedPosition
        }).then(function(data) {
            if (!data.success) return;

            // Pin tonen op cel
            if (selectedPosition !== null) {
                var cel = document.querySelector('.feedback-cel[data-position="' + selectedPosition + '"]');
                if (cel) {
                    cel.classList.add('heeft-feedback');
                    if (!cel.querySelector('.feedback-pin')) {
                        var pin = document.createElement('span');
                        pin.className = 'feedback-pin';
                        pin.setAttribute('aria-hidden', 'true');
                        pin.textContent = '💬';
                        cel.appendChild(pin);
                    }
                }
            }

            // Toevoegen aan lijst
            var leeg = document.getElementById('feedback-leeg');
            if (leeg) leeg.remove();

            var locatieTekst = selectedPosition !== null
            ? (((selectedPosition % window.metroGridCols) + 1) + ',' + (Math.floor(selectedPosition / window.metroGridCols) + 1))
            : '{{ __("Algemeen") }}';

            var item = document.createElement('div');
            item.className = 'feedback-item ' + type;
            item.dataset.id = data.id;
            item.innerHTML =
                '<span class="feedback-item-icon">' + (type === 'improvement' ? '💡' : '💬') + '</span>' +
                '<div class="feedback-item-body">' +
                    '<span class="feedback-item-locatie">' + locatieTekst + '</span>' +
                    '<p class="feedback-item-tekst">' + comment + '</p>' +
                '</div>' +
                '<button type="button" class="btn-delete-function fb-verwijder-btn" data-id="' + data.id + '" aria-label="{{ __("Verwijder opmerking") }}">{{ __("Verwijderen") }}</button>';

            document.getElementById('feedback-lijst').appendChild(item);

            // Reset
            document.getElementById('fb-comment').value = '';
            document.getElementById('feedback-locatie-label').textContent = '';
            document.querySelectorAll('.feedback-cel.feedback-actief').forEach(function(c) {
                c.classList.remove('feedback-actief');
            });
            selectedPosition = null;
        });
    });

    // Verwijderen
    document.getElementById('feedback-lijst').addEventListener('click', function(e) {
        var btn = e.target.closest('.fb-verwijder-btn');
        if (!btn) return;
        var id   = btn.dataset.id;
        var item = document.querySelector('.feedback-item[data-id="' + id + '"]');
        deleteJson('/beleidsmaker/feedback/' + id).then(function(data) {
            if (data.success && item) item.remove();
        });
    });

    // Goedkeuren / afkeuren
    function verstuur(status) {
        var fout = document.getElementById('fb-submit-fout');
        postJson('{{ route("beleidsmaker.feedback.submit") }}', { status: status })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    fout.textContent = '{{ __("Er is een fout opgetreden.") }}';
                    fout.hidden = false;
                }
            });
    }

    var goedkeuren = document.getElementById('fb-goedkeuren');
    var afkeuren   = document.getElementById('fb-afkeuren');
    if (goedkeuren) goedkeuren.addEventListener('click', function() { verstuur('approved'); });
    if (afkeuren)   afkeuren.addEventListener('click', function() { verstuur('declined'); });
})();
</script>
@endsection