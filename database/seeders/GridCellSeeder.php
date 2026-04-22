<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GridCell;

class GridCellSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 12; $i++) {
            GridCell::create([
                'position' => $i,
                'destination_id' => null,
            ]);
        }
    }
}
