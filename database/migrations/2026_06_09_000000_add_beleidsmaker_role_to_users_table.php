<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gebruiker', 'viewer', 'beleidsmaker') NOT NULL DEFAULT 'viewer'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'viewer' WHERE role = 'beleidsmaker'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gebruiker', 'viewer') NOT NULL DEFAULT 'viewer'");
    }
};
