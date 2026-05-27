<?php

// Regels voor functies die naast elkaar geplaatst worden.

return [

    // Aantal kolommen van het grid (gebruikt om buren te bepalen)
    'grid_cols' => 4,

    // Paren die NIET naast elkaar mogen staan.
    // Een drop op een cel naast een van deze combi's wordt geweigerd.
    'forbidden_pairs' => [
        ['Park',       'Tankstation'],   // rust naast vervuiling
        ['Park',       'Weg'],           // rust naast verkeer
        ['Ziekenhuis', 'Tankstation'],   // gevaarlijk bij benzinedampen
        ['School',     'Tankstation'],   // kinderen niet bij brandstof
    ],

    // Bonussen: extra punten als deze twee functies naast elkaar staan.
    'bonus_pairs' => [
        ['functions' => ['Politiebureau', 'Brandweerkazerne'], 'category' => 'veiligheid',   'points' => 2],
        ['functions' => ['School',        'Park'],             'category' => 'voorzieningen', 'points' => 2],
        ['functions' => ['Ziekenhuis',    'Politiebureau'],    'category' => 'veiligheid',   'points' => 1],
        ['functions' => ['Station',       'Winkel'],           'category' => 'mobiliteit',   'points' => 2],
    ],

];
