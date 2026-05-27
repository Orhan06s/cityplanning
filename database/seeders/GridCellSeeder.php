<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GridCell;

class GridCellSeeder extends Seeder
{
    public function run(): void
    {
        // Max grid size is 64 → maak alle cellen vooraf aan
        for ($i = 0; $i < 64; $i++) {
            GridCell::create([
                'position' => $i,
                'destination_id' => null,
            ]);
        }
    }
}
