<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grid_cells', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('is_destination');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('grid_cells', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'approved_by']);
        });
    }
};
