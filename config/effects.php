<?php

// Tabel van effect-punten per functie, per categorie.
// Categorienamen zijn lowercase zodat ze matchen met destinations.category.

return [

    'categories' => [
        'veiligheid',
        'recreatie',
        'milieukwaliteit',
        'voorzieningen',
        'mobiliteit',
    ],

    'functions' => [
        // Veiligheid
        'Politiebureau'    => ['veiligheid' =>  5, 'recreatie' => 1, 'milieukwaliteit' =>  0, 'voorzieningen' => 1, 'mobiliteit' =>  2],
        'Brandweerkazerne' => ['veiligheid' =>  4, 'recreatie' => 1, 'milieukwaliteit' =>  2, 'voorzieningen' => 1, 'mobiliteit' =>  2],
        // Recreatie
        'Park'             => ['veiligheid' => -2, 'recreatie' => 5, 'milieukwaliteit' =>  4, 'voorzieningen' => 0, 'mobiliteit' =>  0],
        'Bioscoop'         => ['veiligheid' => -1, 'recreatie' => 4, 'milieukwaliteit' =>  0, 'voorzieningen' => 2, 'mobiliteit' =>  0],
        'Sportpark'        => ['veiligheid' =>  0, 'recreatie' => 5, 'milieukwaliteit' =>  2, 'voorzieningen' => 3, 'mobiliteit' =>  0],
        // Milieukwaliteit
        'Waterzuivering'   => ['veiligheid' =>  0, 'recreatie' => 0, 'milieukwaliteit' =>  5, 'voorzieningen' => 2, 'mobiliteit' =>  0],
        // Voorzieningen
        'School'           => ['veiligheid' =>  2, 'recreatie' => 2, 'milieukwaliteit' =>  0, 'voorzieningen' => 5, 'mobiliteit' => -3],
        'Winkel'           => ['veiligheid' =>  0, 'recreatie' => 0, 'milieukwaliteit' => -2, 'voorzieningen' => 5, 'mobiliteit' =>  0],
        'Ziekenhuis'       => ['veiligheid' =>  3, 'recreatie' => 0, 'milieukwaliteit' =>  0, 'voorzieningen' => 5, 'mobiliteit' =>  0],
        // Mobiliteit
        'Station'          => ['veiligheid' => -2, 'recreatie' => 2, 'milieukwaliteit' =>  0, 'voorzieningen' => 4, 'mobiliteit' =>  5],
        'Weg'              => ['veiligheid' => -4, 'recreatie' => 2, 'milieukwaliteit' => -4, 'voorzieningen' => 3, 'mobiliteit' =>  5],
        'Fietspad'         => ['veiligheid' =>  0, 'recreatie' => 3, 'milieukwaliteit' =>  3, 'voorzieningen' => 3, 'mobiliteit' =>  4],
        'Tankstation'      => ['veiligheid' => -2, 'recreatie' => 0, 'milieukwaliteit' => -4, 'voorzieningen' => 1, 'mobiliteit' =>  4],
    ],

];