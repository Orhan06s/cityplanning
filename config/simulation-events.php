<?php

// Vooraf gedefinieerde simulatie-evenementen.
// Elk evenement heeft een effect per categorie (zelfde categorieën als effects.php)
// dat wordt opgeteld bij de totalen wanneer het evenement actief is.

return [

    'types' => [
        'carnaval' => [
            'label' => 'Carnaval',
            'icon'  => '🎭',
            'effects' => [
                'veiligheid'      => -2,
                'recreatie'       => 5,
                'milieukwaliteit' => -1,
                'voorzieningen'   => 3,
                'mobiliteit'      => -3,
            ],
        ],
        'kermis' => [
            'label' => 'Kermis',
            'icon'  => '🎡',
            'effects' => [
                'veiligheid'      => -1,
                'recreatie'       => 5,
                'milieukwaliteit' => -2,
                'voorzieningen'   => 2,
                'mobiliteit'      => -2,
            ],
        ],
        'muziekfestival' => [
            'label' => 'Muziekfestival',
            'icon'  => '🎶',
            'effects' => [
                'veiligheid'      => -2,
                'recreatie'       => 4,
                'milieukwaliteit' => -3,
                'voorzieningen'   => 1,
                'mobiliteit'      => -4,
            ],
        ],
    ],

];
