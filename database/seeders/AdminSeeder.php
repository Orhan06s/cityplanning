<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->updateOrInsert(
            ['id' => 1],
            [
                'name'       => 'Admin',
                'email'      => 't.ezaim@student.avans.nl',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
