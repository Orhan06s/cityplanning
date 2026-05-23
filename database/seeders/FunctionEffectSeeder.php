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
        ['function_name' => 'Politiebureau',   'category' => 'veiligheid',      'points' => 5],
        ['function_name' => 'Politiebureau',   'category' => 'recreatie',       'points' => 1],
        ['function_name' => 'Politiebureau',   'category' => 'milieukwaliteit', 'points' => 0],
        ['function_name' => 'Politiebureau',   'category' => 'voorzieningen',   'points' => 1],
        ['function_name' => 'Politiebureau',   'category' => 'mobiliteit',      'points' => 2],

        ['function_name' => 'Brandweerkazerne','category' => 'veiligheid',      'points' => 4],
        ['function_name' => 'Brandweerkazerne','category' => 'recreatie',       'points' => 1],
        ['function_name' => 'Brandweerkazerne','category' => 'milieukwaliteit', 'points' => 2],
        ['function_name' => 'Brandweerkazerne','category' => 'voorzieningen',   'points' => 1],
        ['function_name' => 'Brandweerkazerne','category' => 'mobiliteit',      'points' => 2],

        ['function_name' => 'Park',            'category' => 'veiligheid',      'points' => -2],
        ['function_name' => 'Park',            'category' => 'recreatie',       'points' => 5],
        ['function_name' => 'Park',            'category' => 'milieukwaliteit', 'points' => 4],
        ['function_name' => 'Park',            'category' => 'voorzieningen',   'points' => 0],
        ['function_name' => 'Park',            'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'Bioscoop',        'category' => 'veiligheid',      'points' => -1],
        ['function_name' => 'Bioscoop',        'category' => 'recreatie',       'points' => 4],
        ['function_name' => 'Bioscoop',        'category' => 'milieukwaliteit', 'points' => 0],
        ['function_name' => 'Bioscoop',        'category' => 'voorzieningen',   'points' => 2],
        ['function_name' => 'Bioscoop',        'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'Sportpark',       'category' => 'veiligheid',      'points' => 0],
        ['function_name' => 'Sportpark',       'category' => 'recreatie',       'points' => 5],
        ['function_name' => 'Sportpark',       'category' => 'milieukwaliteit', 'points' => 2],
        ['function_name' => 'Sportpark',       'category' => 'voorzieningen',   'points' => 3],
        ['function_name' => 'Sportpark',       'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'Waterzuivering',  'category' => 'veiligheid',      'points' => 0],
        ['function_name' => 'Waterzuivering',  'category' => 'recreatie',       'points' => 0],
        ['function_name' => 'Waterzuivering',  'category' => 'milieukwaliteit', 'points' => 5],
        ['function_name' => 'Waterzuivering',  'category' => 'voorzieningen',   'points' => 2],
        ['function_name' => 'Waterzuivering',  'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'School',          'category' => 'veiligheid',      'points' => 2],
        ['function_name' => 'School',          'category' => 'recreatie',       'points' => 2],
        ['function_name' => 'School',          'category' => 'milieukwaliteit', 'points' => 0],
        ['function_name' => 'School',          'category' => 'voorzieningen',   'points' => 5],
        ['function_name' => 'School',          'category' => 'mobiliteit',      'points' => -3],

        ['function_name' => 'Winkel',          'category' => 'veiligheid',      'points' => 0],
        ['function_name' => 'Winkel',          'category' => 'recreatie',       'points' => 0],
        ['function_name' => 'Winkel',          'category' => 'milieukwaliteit', 'points' => -2],
        ['function_name' => 'Winkel',          'category' => 'voorzieningen',   'points' => 5],
        ['function_name' => 'Winkel',          'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'Ziekenhuis',      'category' => 'veiligheid',      'points' => 3],
        ['function_name' => 'Ziekenhuis',      'category' => 'recreatie',       'points' => 0],
        ['function_name' => 'Ziekenhuis',      'category' => 'milieukwaliteit', 'points' => 0],
        ['function_name' => 'Ziekenhuis',      'category' => 'voorzieningen',   'points' => 5],
        ['function_name' => 'Ziekenhuis',      'category' => 'mobiliteit',      'points' => 0],

        ['function_name' => 'Station',         'category' => 'veiligheid',      'points' => -2],
        ['function_name' => 'Station',         'category' => 'recreatie',       'points' => 2],
        ['function_name' => 'Station',         'category' => 'milieukwaliteit', 'points' => -4],
        ['function_name' => 'Station',         'category' => 'voorzieningen',   'points' => 4],
        ['function_name' => 'Station',         'category' => 'mobiliteit',      'points' => 5],

        ['function_name' => 'Weg',             'category' => 'veiligheid',      'points' => -4],
        ['function_name' => 'Weg',             'category' => 'recreatie',       'points' => 2],
        ['function_name' => 'Weg',             'category' => 'milieukwaliteit', 'points' => -4],
        ['function_name' => 'Weg',             'category' => 'voorzieningen',   'points' => 3],
        ['function_name' => 'Weg',             'category' => 'mobiliteit',      'points' => 5],

        ['function_name' => 'Fietspad',        'category' => 'veiligheid',      'points' => 0],
        ['function_name' => 'Fietspad',        'category' => 'recreatie',       'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'milieukwaliteit', 'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'voorzieningen',   'points' => 3],
        ['function_name' => 'Fietspad',        'category' => 'mobiliteit',      'points' => 4],

        ['function_name' => 'Tankstation',     'category' => 'veiligheid',      'points' => -2],
        ['function_name' => 'Tankstation',     'category' => 'recreatie',       'points' => 0],
        ['function_name' => 'Tankstation',     'category' => 'milieukwaliteit', 'points' => -4],
        ['function_name' => 'Tankstation',     'category' => 'voorzieningen',   'points' => 1],
        ['function_name' => 'Tankstation',     'category' => 'mobiliteit',      'points' => 4],
    ];

    DB::table('function_effects')->insert($effects);
    }
}
