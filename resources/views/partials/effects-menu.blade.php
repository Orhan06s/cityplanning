{{-- Horizontaal menu bovenaan: per functie een dropdown met de 5 effect-waarden --}}
<nav class="effecten-menu" aria-label="{{ __('Effectenmatrix') }}">
    <ul class="effecten-menu-lijst">
        @foreach($effectsMatrix as $functionName => $effecten)
            <li class="effecten-menu-item">
                <details class="effecten-details">
                    <summary class="effecten-summary">
                        {{ __($functionName) }}
                        <span class="effecten-chevron" aria-hidden="true">▾</span>
                    </summary>
                    <div class="effecten-dropdown" role="region" aria-label="{{ __($functionName) }} {{ __('effecten') }}">
                        @foreach($effecten as $cat => $waarde)
                            @php
                                $teken = $waarde > 0 ? '+' . $waarde : (string) $waarde;
                                $kleur = $waarde > 0 ? 'positief' : ($waarde < 0 ? 'negatief' : 'neutraal');
                            @endphp
                            <div class="effecten-rij">
                                <span class="effecten-cat">{{ __($cat) }}</span>
                                <span class="effecten-waarde is-{{ $kleur }}">{{ $teken }}</span>
                            </div>
                        @endforeach
                    </div>
                </details>
            </li>
        @endforeach
    </ul>
</nav>
