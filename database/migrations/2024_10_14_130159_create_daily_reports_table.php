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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->date('day')->unique();
            $table->float('temperature',2)->nullable();
            $table->float('dew_point',2)->nullable();
            $table->float('pressure',2)->nullable();
            $table->integer('humidity')->nullable();
            $table->float('rain_rate',2)->nullable();
            $table->float('gust_speed',2)->nullable();
            $table->float('wind_speed',2)->nullable();
            $table->integer('wind_dir')->nullable();
            $table->boolean('sync')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
