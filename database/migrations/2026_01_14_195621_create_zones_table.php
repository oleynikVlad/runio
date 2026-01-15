<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_paths', function (Blueprint $table) {
            $table->id();

            $table->geometry('location', 'point', 4326);
            $table->integer('x')->index(); // Округлена довгота
            $table->integer('y')->index(); // Округлена широта
            $table->foreignId('user_id')->constrained();
            $table->string('color', 7);
            $table->timestamps();

            $table->spatialIndex('location');
            $table->unique(['x', 'y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_paths');
    }
};
