<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DayNightModifier;

class DayNightModifiersSeeder extends Seeder
{
    public function run()
    {
        // Clear existing
        DayNightModifier::truncate();

        // Seed modifiers for each function × category combination
        $modifiers = [
            // Politiebureau (Police Station)
            ['function_name' => 'Politiebureau', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -2],
            ['function_name' => 'Politiebureau', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Politiebureau', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Politiebureau', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Politiebureau', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  1],

            // Brandweerkazerne (Fire Station)
            ['function_name' => 'Brandweerkazerne', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -1],
            ['function_name' => 'Brandweerkazerne', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Brandweerkazerne', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Brandweerkazerne', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Brandweerkazerne', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  1],

            // Park
            ['function_name' => 'Park', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -3],
            ['function_name' => 'Park', 'category' => 'recreatie',       'day_modifier' =>  2, 'night_modifier' => -2],
            ['function_name' => 'Park', 'category' => 'milieukwaliteit', 'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Park', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Park', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  0],

            // Bioscoop (Cinema) 
            ['function_name' => 'Bioscoop', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -1],
            ['function_name' => 'Bioscoop', 'category' => 'recreatie',       'day_modifier' => -1, 'night_modifier' =>  2],
            ['function_name' => 'Bioscoop', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0], 
            ['function_name' => 'Bioscoop', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  1],
            ['function_name' => 'Bioscoop', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  1],

            // Sportpark
            ['function_name' => 'Sportpark', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -2],
            ['function_name' => 'Sportpark', 'category' => 'recreatie',       'day_modifier' =>  2, 'night_modifier' => -2],
            ['function_name' => 'Sportpark', 'category' => 'milieukwaliteit', 'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Sportpark', 'category' => 'voorzieningen',   'day_modifier' =>  1, 'night_modifier' => -1],
            ['function_name' => 'Sportpark', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  0],

            // Waterzuivering (Water Treatment)
            ['function_name' => 'Waterzuivering', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Waterzuivering', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Waterzuivering', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Waterzuivering', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Waterzuivering', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  0],

            // School
            ['function_name' => 'School', 'category' => 'veiligheid',      'day_modifier' =>  1, 'night_modifier' => -2],
            ['function_name' => 'School', 'category' => 'recreatie',       'day_modifier' =>  1, 'night_modifier' => -2],
            ['function_name' => 'School', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'School', 'category' => 'voorzieningen',   'day_modifier' =>  2, 'night_modifier' => -3],
            ['function_name' => 'School', 'category' => 'mobiliteit',      'day_modifier' => -1, 'night_modifier' =>  1],

            // Winkel (Shop)
            ['function_name' => 'Winkel', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -1],
            ['function_name' => 'Winkel', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Winkel', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' => -1],
            ['function_name' => 'Winkel', 'category' => 'voorzieningen',   'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Winkel', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  0],

            // Ziekenhuis (Hospital)
            ['function_name' => 'Ziekenhuis', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' =>  2],
            ['function_name' => 'Ziekenhuis', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Ziekenhuis', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Ziekenhuis', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  2],
            ['function_name' => 'Ziekenhuis', 'category' => 'mobiliteit',      'day_modifier' =>  0, 'night_modifier' =>  1],

            // Station
            ['function_name' => 'Station', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -3],
            ['function_name' => 'Station', 'category' => 'recreatie',       'day_modifier' =>  1, 'night_modifier' =>  1],
            ['function_name' => 'Station', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' => -2],
            ['function_name' => 'Station', 'category' => 'voorzieningen',   'day_modifier' =>  1, 'night_modifier' =>  2],
            ['function_name' => 'Station', 'category' => 'mobiliteit',      'day_modifier' =>  1, 'night_modifier' => -2],

            // Weg (Road)
            ['function_name' => 'Weg', 'category' => 'veiligheid',      'day_modifier' =>  1, 'night_modifier' => -3],
            ['function_name' => 'Weg', 'category' => 'recreatie',       'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Weg', 'category' => 'milieukwaliteit', 'day_modifier' =>  1, 'night_modifier' => -2],
            ['function_name' => 'Weg', 'category' => 'voorzieningen',   'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Weg', 'category' => 'mobiliteit',      'day_modifier' =>  1, 'night_modifier' =>  2],

            // Fietspad (Bike Path)
            ['function_name' => 'Fietspad', 'category' => 'veiligheid',      'day_modifier' =>  1, 'night_modifier' => -2],
            ['function_name' => 'Fietspad', 'category' => 'recreatie',       'day_modifier' =>  2, 'night_modifier' =>  0],
            ['function_name' => 'Fietspad', 'category' => 'milieukwaliteit', 'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Fietspad', 'category' => 'voorzieningen',   'day_modifier' =>  1, 'night_modifier' =>  0],
            ['function_name' => 'Fietspad', 'category' => 'mobiliteit',      'day_modifier' =>  1, 'night_modifier' =>  1],

            // Tankstation (Gas Station)
            ['function_name' => 'Tankstation', 'category' => 'veiligheid',      'day_modifier' =>  0, 'night_modifier' => -3],
            ['function_name' => 'Tankstation', 'category' => 'recreatie',       'day_modifier' =>  0, 'night_modifier' =>  0],
            ['function_name' => 'Tankstation', 'category' => 'milieukwaliteit', 'day_modifier' =>  0, 'night_modifier' => -2],
            ['function_name' => 'Tankstation', 'category' => 'voorzieningen',   'day_modifier' =>  0, 'night_modifier' =>  1],
            ['function_name' => 'Tankstation', 'category' => 'mobiliteit',      'day_modifier' =>  1, 'night_modifier' =>  2],
        ];

        foreach ($modifiers as $modifier) {
            DayNightModifier::create($modifier);
        }

    }
}