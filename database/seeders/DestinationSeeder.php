<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            // Veiligheid
            ['name' => 'Politiebureau',   'name_en' => 'Police station', 'category' => 'veiligheid',      'icon' => '🚓', 'description' => 'Een politiebureau voor veiligheid'],
            ['name' => 'Brandweerkazerne','name_en' => 'Fire station',   'category' => 'veiligheid',      'icon' => '🚒', 'description' => 'Een brandweerkazerne'],

            // Recreatie
            ['name' => 'Park',            'name_en' => 'Park',           'category' => 'recreatie',       'icon' => '🌳', 'description' => 'Een park met bomen en gras'],
            ['name' => 'Bioscoop',        'name_en' => 'Cinema',         'category' => 'recreatie',       'icon' => '🎬', 'description' => 'Een bioscoop voor films'],
            ['name' => 'Sportpark',       'name_en' => 'Sports park',    'category' => 'recreatie',       'icon' => '⚽', 'description' => 'Een sportpark voor recreatie'],

            // Milieukwaliteit
            ['name' => 'Waterzuivering',  'name_en' => 'Water treatment','category' => 'milieukwaliteit', 'icon' => '💧', 'description' => 'Een waterzuiveringsinstallatie'],

            // Voorzieningen
            ['name' => 'School',          'name_en' => 'School',         'category' => 'voorzieningen',   'icon' => '🏫', 'description' => 'Een school voor onderwijs'],
            ['name' => 'Winkel',          'name_en' => 'Shop',           'category' => 'voorzieningen',   'icon' => '🏬', 'description' => 'Een winkel voor boodschappen'],
            ['name' => 'Ziekenhuis',      'name_en' => 'Hospital',       'category' => 'voorzieningen',   'icon' => '🏥', 'description' => 'Een ziekenhuis voor medische zorg'],

            // Mobiliteit
            ['name' => 'Station',         'name_en' => 'Train station',  'category' => 'mobiliteit',      'icon' => '🚉', 'description' => 'Een treinstation'],
            ['name' => 'Weg',             'name_en' => 'Road',           'category' => 'mobiliteit',      'icon' => '🛣️', 'description' => 'Een weg voor verkeer'],
            ['name' => 'Fietspad',        'name_en' => 'Cycle path',     'category' => 'mobiliteit',      'icon' => '🚲', 'description' => 'Een fietspad'],
            ['name' => 'Tankstation',     'name_en' => 'Gas station',    'category' => 'mobiliteit',      'icon' => '⛽', 'description' => 'Een tankstation'],
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
