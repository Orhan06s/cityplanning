@php
    $catLabels = [
        'veiligheid'      => __('veiligheid'),
        'recreatie'       => __('recreatie'),
        'milieukwaliteit' => __('milieukwaliteit'),
        'voorzieningen'   => __('voorzieningen'),
        'mobiliteit'      => __('mobiliteit'),
    ];
@endphp

<div class="totalen-paneel" role="region" aria-label="{{ __('Effectenmatrix') }}">
    <div class="totalen-titel">{{ __('Effecten per stadsfunctie en categorie') }}</div>
    <div class="totalen-grid">
        @foreach($categories as $cat)
            @php
                $score   = $effecten[$cat] ?? 0;
                $klasse  = $score > 0 ? 'totaal-positief' : ($score < 0 ? 'totaal-negatief' : 'totaal-neutraal');
                $teken   = $score > 0 ? "+{$score}" : (string) $score;
                $label   = $catLabels[$cat] ?? $cat;
            @endphp
            <div class="totaal-kaart {{ $klasse }}">
                <span class="totaal-label">{{ $label }}</span>
                <span class="totaal-score">{{ $teken }}</span>
            </div>
        @endforeach
    </div>
</div>
