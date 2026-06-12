<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('day_night_modifiers', function (Blueprint $table) {
            $table->id();
            $table->string('function_name');
            $table->string('category');
            $table->integer('day_modifier')->default(0);   
            $table->integer('night_modifier')->default(0); 
            $table->timestamps();

            $table->unique(['function_name', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('day_night_modifiers');
    }
};