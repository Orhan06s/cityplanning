<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FunctionEffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $effects = [
        ['function_name' => 'Politiebureau',   'category' => 'Veiligheid',      'points' => 5],
        ['function_name' => 'Politiebureau',   'category' => 'Recreatie',       'points' => 1],
        ['function_name' => 'Politiebureau',   'category' => 'Milieukwaliteit', 'points' => 0],
        ['function_name' => 'Politiebureau',   'category' => 'Voorzieningen',   'points' => 1],
        ['function_name' => 'Politiebureau',   'category' => 'Mobiliteit',      'points' => 2],

        ['function_name' => 'Brandweerkazerne','category' => 'Veiligheid',      'points' => 4],
        ['function_name' => 'Brandweerkazerne','category' => 'Recreatie',       'points' => 1],
        ['function_name' => 'Brandweerkazerne','category' => 'Milieukwaliteit', 'points' => 2],
        ['function_name' => 'Brandweerkazerne','category' => 'Voorzieningen',   'points' => 1],
        ['function_name' => 'Brandweerkazerne','category' => 'Mobiliteit',      'points' => 2],

        ['function_name' => 'Park',            'category' => 'Veiligheid',      'points' => -2],
        ['function_name' => 'Park',            'category' => 'Recreatie',       'points' => 5],
        ['function_name' => 'Park',            'category' => 'Milieukwaliteit', 'points' => 4],
        ['function_name' => 'Park',            'category' => 'Voorzieningen',   'points' => 0],
        ['function_name' => 'Park',            'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'Bioscoop',        'category' => 'Veiligheid',      'points' => -1],
        ['function_name' => 'Bioscoop',        'category' => 'Recreatie',       'points' => 4],
        ['function_name' => 'Bioscoop',        'category' => 'Milieukwaliteit', 'points' => 0],
        ['function_name' => 'Bioscoop',        'category' => 'Voorzieningen',   'points' => 2],
        ['function_name' => 'Bioscoop',        'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'Sportpark',       'category' => 'Veiligheid',      'points' => 0],
        ['function_name' => 'Sportpark',       'category' => 'Recreatie',       'points' => 5],
        ['function_name' => 'Sportpark',       'category' => 'Milieukwaliteit', 'points' => 2],
        ['function_name' => 'Sportpark',       'category' => 'Voorzieningen',   'points' => 3],
        ['function_name' => 'Sportpark',       'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'Waterzuivering',  'category' => 'Veiligheid',      'points' => 0],
        ['function_name' => 'Waterzuivering',  'category' => 'Recreatie',       'points' => 0],
        ['function_name' => 'Waterzuivering',  'category' => 'Milieukwaliteit', 'points' => 5],
        ['function_name' => 'Waterzuivering',  'category' => 'Voorzieningen',   'points' => 2],
        ['function_name' => 'Waterzuivering',  'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'School',          'category' => 'Veiligheid',      'points' => 2],
        ['function_name' => 'School',          'category' => 'Recreatie',       'points' => 2],
        ['function_name' => 'School',          'category' => 'Milieukwaliteit', 'points' => 0],
        ['function_name' => 'School',          'category' => 'Voorzieningen',   'points' => 5],
        ['function_name' => 'School',          'category' => 'Mobiliteit',      'points' => -3],

        ['function_name' => 'Winkel',          'category' => 'Veiligheid',      'points' => 0],
        ['function_name' => 'Winkel',          'category' => 'Recreatie',       'points' => 0],
        ['function_name' => 'Winkel',          'category' => 'Milieukwaliteit', 'points' => -2],
        ['function_name' => 'Winkel',          'category' => 'Voorzieningen',   'points' => 5],
        ['function_name' => 'Winkel',          'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'Ziekenhuis',      'category' => 'Veiligheid',      'points' => 3],
        ['function_name' => 'Ziekenhuis',      'category' => 'Recreatie',       'points' => 0],
        ['function_name' => 'Ziekenhuis',      'category' => 'Milieukwaliteit', 'points' => 0],
        ['function_name' => 'Ziekenhuis',      'category' => 'Voorzieningen',   'points' => 5],
        ['function_name' => 'Ziekenhuis',      'category' => 'Mobiliteit',      'points' => 0],

        ['function_name' => 'Station',         'category' => 'Veiligheid',      'points' => -2],
        ['function_name' => 'Station',         'category' => 'Recreatie',       'points' => 2],
        ['function_name' => 'Station',         'category' => 'Milieukwaliteit', 'points' => 0],
        ['function_name' => 'Station',         'category' => 'Voorzieningen',   'points' => 4],
        ['function_name' => 'Station',         'category' => 'Mobiliteit',      'points' => 5],

        ['function_name' => 'Weg',             'category' => 'Veiligheid',      'points' => -4],
        ['function_name' => 'Weg',             'category' => 'Recreatie',       'points' => 2],
        ['function_name' => 'Weg',             'category' => 'Milieukwaliteit', 'points' => -4],
        ['function_name' => 'Weg',             'category' => 'Voorzieningen',   'points' => 3],
        ['function_name' => 'Weg',             'category' => 'Mobiliteit',      'points' => 5],

        ['function_name' => 'Fietspad',        'category' => 'Veiligheid',      'points' => 0],
        ['function_name' => 'Fietspad',        'category' => 'Recreatie',       'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'Milieukwaliteit', 'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'Voorzieningen',   'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'Mobiliteit',      'points' => 4],

        ['function_name' => 'Tankstation',     'category' => 'Veiligheid',      'points' => -2],
        ['function_name' => 'Tankstation',     'category' => 'Recreatie',       'points' => 0],
        ['function_name' => 'Tankstation',     'category' => 'Milieukwaliteit', 'points' => -4],
        ['function_name' => 'Tankstation',     'category' => 'Voorzieningen',   'points' => 1],
        ['function_name' => 'Tankstation',     'category' => 'Mobiliteit',      'points' => 4],
    ];

    DB::table('function_effects')->insert($effects);
}
    }

