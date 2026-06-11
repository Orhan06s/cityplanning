<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('location');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Bestaande rijen (duur in minuten) omzetten naar een datumrange vanaf vandaag.
        DB::table('simulation_events')->get()->each(function ($event) {
            DB::table('simulation_events')->where('id', $event->id)->update([
                'start_date' => now()->toDateString(),
                'end_date'   => now()->addMinutes($event->duration ?? 0)->toDateString(),
            ]);
        });

        Schema::table('simulation_events', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }

    public function down(): void
    {
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->integer('duration')->default(60)->after('location');
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
