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
        Schema::create('weather_stations', function (Blueprint $table) {
            $table->id();
            $table->float('temperature',2)->nullable();
            $table->float('humidity',2)->nullable();
            $table->float('pressure',2)->nullable();
            $table->float('dewpoint',2)->nullable();
            $table->float('rainrate',2)->nullable();
            $table->float('windspeed',2)->nullable();
            $table->float('winddir',2)->nullable();
            $table->float('windgust',2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_stations');
    }
};
