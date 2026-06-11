<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('location')->nullable(); // grid-positie, null = hele grid
            $table->integer('duration'); // duur in minuten
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_events');
    }
};
