<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->nullable(); // null = algemene opmerking
            $table->text('comment');
            $table->enum('type', ['comment', 'improvement'])->default('comment');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->boolean('submitted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_feedback');
    }
};