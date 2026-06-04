<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grid_cells', function (Blueprint $table) {
            $table->boolean('is_destination')->default(false)->after('linked_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('grid_cells', function (Blueprint $table) {
            $table->dropColumn('is_destination');
        });
    }
};
