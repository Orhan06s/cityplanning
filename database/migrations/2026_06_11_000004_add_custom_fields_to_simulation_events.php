<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->string('custom_label', 100)->nullable()->after('type');
            $table->string('custom_icon', 10)->nullable()->after('custom_label');
        });
    }

    public function down(): void
    {
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->dropColumn(['custom_label', 'custom_icon']);
        });
    }
};
