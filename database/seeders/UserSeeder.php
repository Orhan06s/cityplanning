<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@metropolis.nl'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'planner@metropolis.nl'],
            [
                'name'     => 'City Planner',
                'password' => Hash::make('password'),
                'role'     => 'gebruiker',
            ]
        );

        User::firstOrCreate(
            ['email' => 'viewer@metropolis.nl'],
            [
                'name'     => 'Viewer',
                'password' => Hash::make('password'),
                'role'     => 'viewer',
            ]
        );

        User::firstOrCreate(
            ['email' => 'beleidsmaker@metropolis.nl'],
            [
                'name'     => 'Beleidsmaker',
                'password' => Hash::make('password'),
                'role'     => 'beleidsmaker',
            ]
        );
    }
}
