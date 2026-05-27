<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Wijzig de enum kolom om 'gebruiker' toe te voegen (no-op bij verse install)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gebruiker', 'viewer') NOT NULL DEFAULT 'viewer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer'");
    }
};
