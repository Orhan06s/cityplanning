@extends('layouts.app')

@section('title', __('Evenementen') . ' — Metropolis')

@section('content')

<div class="manager-pagina">

    <div class="manager-pagina-header">
        <h2 class="manager-pagina-titel">{{ __('Simulatie-evenementen') }}</h2>
    </div>

    <p class="manager-pagina-tekst">
        {{ __('Plan evenementen in en bekijk direct het effect op het stadsgebied.') }}
    </p>

    @auth
        @if(auth()->user()->canEdit())
            <div class="effects-manager-toolbar">
                <button id="add-event-btn" type="button" class="btn btn-add" aria-label="{{ __('Voeg nieuw evenement toe') }}">
                    <span aria-hidden="true">+</span> {{ __('Evenement toevoegen') }}
                </button>

                @if($events->isNotEmpty())
                    <button id="clear-events-btn" type="button" class="btn btn-danger">
                        {{ __('Alle evenementen wissen') }}
                    </button>
                @endif
            </div>
        @endif
    @endauth

    <div class="effects-manager-tabel-wrapper" tabindex="0">
        <table class="effects-manager-tabel" role="table" aria-label="{{ __('Overzicht van simulatie-evenementen') }}">
            <thead>
                <tr role="row">
                    <th scope="col">{{ __('Type evenement') }}</th>
                    <th scope="col">{{ __('Locatie') }}</th>
                    <th scope="col">{{ __('Periode') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col" class="th-actions" aria-label="{{ __('Acties') }}"></th>
                </tr>
            </thead>
            <tbody id="events-tbody">
                @forelse($events as $event)
                    @php
                        $typeInfo = $eventTypes[$event->type] ?? null;
                        if ($event->isGridWide()) {
                            $locatieLabel = __('Hele grid');
                        } else {
                            $col = ($event->location % $gridCols) + 1;
                            $row = intdiv($event->location, $gridCols) + 1;
                            $locatieLabel = $col . ',' . $row;
                        }
                    @endphp
                    <tr role="row" class="events-row" data-event-type="{{ $event->type }}">
                        <td class="td-event-type">
                            <span class="events-icon" aria-hidden="true">{{ $event->type === 'custom' ? ($event->custom_icon ?: '🎉') : ($typeInfo['icon'] ?? '⚠️') }}</span>
                            {{ $event->type === 'custom' ? $event->custom_label : __($typeInfo['label'] ?? $event->type) }}
                        </td>
                        <td>{{ $locatieLabel }}</td>
                        <td>
                            {{ $event->start_date->format('d-m-Y') }}
                            @if($event->start_time) {{ \Illuminate\Support\Str::substr($event->start_time, 0, 5) }}@endif
                            &rarr;
                            {{ $event->end_date->format('d-m-Y') }}
                            @if($event->end_time) {{ \Illuminate\Support\Str::substr($event->end_time, 0, 5) }}@endif
                        </td>
                        <td>
                            <span class="event-status event-status-{{ $event->status }}">
                                {{ __(ucfirst($event->status)) }}
                            </span>
                        </td>
                        <td class="td-actions">
                            @auth
                                @if(auth()->user()->canEdit())
                                    <button type="button" class="btn-delete-function remove-event-btn" data-event-id="{{ $event->id }}" aria-label="{{ __('Verwijder evenement') }}">
                                        {{ __('Verwijderen') }}
                                    </button>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @empty
                    <tr id="events-empty-row">
                        <td colspan="5" class="effects-empty">
                            {{ __('Er zijn momenteel geen actieve evenementen.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@auth
    @if(auth()->user()->canEdit())
        <div id="add-event-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="add-event-modal-titel" aria-hidden="true" hidden>
            <div class="modal-venster" role="document">
                <div class="modal-header">
                    <h3 id="add-event-modal-titel" class="modal-titel">{{ __('Evenement toevoegen') }}</h3>
                    <button type="button" class="modal-sluit-btn" id="add-event-modal-sluit" aria-label="{{ __('Sluiten') }}">&times;</button>
                </div>

                <form id="add-event-form" class="modal-form" novalidate>
                    @csrf

                    <div class="modal-fout" id="add-event-fout" role="alert" aria-live="assertive" hidden></div>

                    <div class="form-veld">
                        <label for="ae-type">{{ __('Type evenement') }} <span class="verplicht" aria-hidden="true">*</span></label>
                        <select id="ae-type" name="type" required aria-required="true">
                            @foreach($eventTypes as $key => $info)
                                <option value="{{ $key }}">{{ $info['icon'] }} {{ __($info['label']) }}</option>
                            @endforeach
                            <option value="custom">{{ __('Anders') }}</option>
                        </select>
                    </div>

                    <div id="ae-custom-velden" class="form-veld" hidden>
                        <label for="ae-custom-label">{{ __('Naam evenement') }} <span class="verplicht" aria-hidden="true">*</span></label>
                        <input type="text" id="ae-custom-label" name="custom_label" maxlength="100">
                    </div>

                    <div class="form-rij form-rij-2">
                        <div class="form-veld">
                            <label for="ae-start-date">{{ __('Startdatum') }} <span class="verplicht" aria-hidden="true">*</span></label>
                            <input type="date" id="ae-start-date" name="start_date" required aria-required="true">
                        </div>

                        <div class="form-veld">
                            <label for="ae-start-time">{{ __('Starttijd') }}</label>
                            <input type="time" id="ae-start-time" name="start_time">
                        </div>
                    </div>

                    <div class="form-rij form-rij-2">
                        <div class="form-veld">
                            <label for="ae-end-date">{{ __('Einddatum') }} <span class="verplicht" aria-hidden="true">*</span></label>
                            <input type="date" id="ae-end-date" name="end_date" required aria-required="true">
                        </div>

                        <div class="form-veld">
                            <label for="ae-end-time">{{ __('Eindtijd') }}</label>
                            <input type="time" id="ae-end-time" name="end_time">
                        </div>
                    </div>

                    <div class="form-veld">
                        <label for="ae-location">{{ __('Locatie') }} <span class="verplicht" aria-hidden="true">*</span></label>
                        <select id="ae-location" name="location" required aria-required="true">
                            <option value="">{{ __('Hele grid') }}</option>
                            @foreach($gridCells as $cell)
                                @php
                                    $col = ($cell->position % $gridCols) + 1;
                                    $row = intdiv($cell->position, $gridCols) + 1;
                                @endphp
                                <option value="{{ $cell->position }}">{{ $col }},{{ $row }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-acties">
                        <button type="button" class="btn btn-annuleer" id="add-event-annuleer">{{ __('Annuleer') }}</button>
                        <button type="submit" class="btn btn-add" id="add-event-submit">{{ __('Toevoegen') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endauth

@endsection
