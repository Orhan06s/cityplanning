@php
    $effectenMatrix = [
        'Politiebureau'    => ['veiligheid' =>  5, 'recreatie' =>  1, 'milieukwaliteit' =>  0, 'voorzieningen' =>  1, 'mobiliteit' =>  2],
        'Brandweerkazerne' => ['veiligheid' =>  4, 'recreatie' =>  1, 'milieukwaliteit' =>  2, 'voorzieningen' =>  1, 'mobiliteit' =>  2],
        'Park'             => ['veiligheid' => -2, 'recreatie' =>  5, 'milieukwaliteit' =>  4, 'voorzieningen' =>  0, 'mobiliteit' =>  0],
        'Bioscoop'         => ['veiligheid' => -1, 'recreatie' =>  4, 'milieukwaliteit' =>  0, 'voorzieningen' =>  2, 'mobiliteit' =>  0],
        'Sportpark'        => ['veiligheid' =>  0, 'recreatie' =>  5, 'milieukwaliteit' =>  2, 'voorzieningen' =>  3, 'mobiliteit' =>  0],
        'Waterzuivering'   => ['veiligheid' =>  0, 'recreatie' =>  0, 'milieukwaliteit' =>  5, 'voorzieningen' =>  2, 'mobiliteit' =>  6],
        'School'           => ['veiligheid' =>  2, 'recreatie' =>  2, 'milieukwaliteit' =>  0, 'voorzieningen' =>  5, 'mobiliteit' => -3],
        'Winkel'           => ['veiligheid' =>  0, 'recreatie' =>  0, 'milieukwaliteit' => -2, 'voorzieningen' =>  5, 'mobiliteit' =>  0],
        'Ziekenhuis'       => ['veiligheid' =>  3, 'recreatie' =>  0, 'milieukwaliteit' =>  0, 'voorzieningen' =>  5, 'mobiliteit' =>  0],
        'Station'          => ['veiligheid' => -2, 'recreatie' =>  2, 'milieukwaliteit' => -4, 'voorzieningen' =>  4, 'mobiliteit' =>  5],
        'Weg'              => ['veiligheid' => -4, 'recreatie' =>  2, 'milieukwaliteit' => -4, 'voorzieningen' =>  3, 'mobiliteit' =>  5],
        'Fietspad'         => ['veiligheid' =>  0, 'recreatie' =>  3, 'milieukwaliteit' =>  3, 'voorzieningen' =>  3, 'mobiliteit' =>  4],
        'Tankstation'      => ['veiligheid' => -2, 'recreatie' =>  0, 'milieukwaliteit' => -4, 'voorzieningen' =>  1, 'mobiliteit' =>  4],
    ];

    $categorieen = [
        'veiligheid'      => __('veiligheid'),
        'recreatie'       => __('recreatie'),
        'milieukwaliteit' => __('milieukwaliteit'),
        'voorzieningen'   => __('voorzieningen'),
        'mobiliteit'      => __('mobiliteit'),
    ];
@endphp

<div id="effecten-trigger-wrapper">
    <button
        id="effecten-trigger"
        class="effecten-trigger-btn"
        aria-expanded="false"
        aria-controls="effecten-panel"
        aria-label="{{ __('Toon effectenmatrix') }}"
        type="button"
    >
        <span class="trigger-icon" aria-hidden="true">&#128202;</span>
        <span class="trigger-label">{{ __('Effectenmatrix') }}</span>
        <span class="trigger-chevron" aria-hidden="true">&#9662;</span>
    </button>
</div>

<div
    id="effecten-panel"
    class="effecten-panel"
    role="region"
    aria-labelledby="effecten-titel"
    aria-hidden="true"
    hidden
>
    <div class="effecten-panel-inner">
        <div class="effecten-panel-header">
            <h2 id="effecten-titel" class="effecten-titel">{{ __('Effectenmatrix') }}</h2>
            <button
                id="effecten-sluit"
                class="effecten-sluit-btn"
                aria-label="{{ __('Sluit effectenmatrix') }}"
                type="button"
            >&times;</button>
        </div>

        <p class="effecten-subtitel">
            {{ __('Impact van elke stadsfunctie op de vijf leefbaarheidscategorieen.') }}
        </p>

        <div class="effecten-legenda" role="note" aria-label="{{ __('Kleurlegenda') }}">
            <span class="legenda-item">
                <span class="legenda-kleur effect-positief" aria-hidden="true"></span>
                {{ __('Positief') }}
            </span>
            <span class="legenda-item">
                <span class="legenda-kleur effect-neutraal" aria-hidden="true"></span>
                {{ __('Neutraal') }}
            </span>
            <span class="legenda-item">
                <span class="legenda-kleur effect-negatief" aria-hidden="true"></span>
                {{ __('Negatief') }}
            </span>
        </div>

        <div class="effecten-tabel-wrapper" tabindex="0">
            <table
                class="effecten-tabel"
                role="table"
                aria-label="{{ __('Effecten per stadsfunctie en categorie') }}"
            >
                <thead>
                    <tr role="row">
                        <th scope="col" class="th-functie">{{ __('Functie') }}</th>
                        @foreach($categorieen as $sleutel => $label)
                            <th scope="col" class="th-categorie th-{{ $sleutel }}">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($effectenMatrix as $functie => $scores)
                        <tr role="row" class="effecten-rij" style="animation-delay: {{ $loop->index * 35 }}ms">
                            <th scope="row" class="td-functienaam">{{ __($functie) }}</th>
                            @foreach($categorieen as $sleutel => $catLabel)
                                @php
                                    $score = $scores[$sleutel] ?? 0;
                                    $klasse = $score > 0 ? 'effect-positief' : ($score < 0 ? 'effect-negatief' : 'effect-neutraal');
                                    $label = $score > 0 ? "+{$score}" : (string) $score;
                                @endphp
                                <td
                                    role="cell"
                                    class="td-score {{ $klasse }}"
                                    aria-label="{{ $catLabel }}: {{ $label }}"
                                    tabindex="0"
                                >
                                    <span aria-hidden="true">{{ $label }}</span>
                                    <span class="sr-only">{{ $catLabel }}: {{ $label }}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
