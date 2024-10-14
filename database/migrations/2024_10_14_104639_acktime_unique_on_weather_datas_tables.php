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
        Schema::table('temperatures', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('humidities', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('pressures', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('winds', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('wind_gusts', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('dew_points', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });
        Schema::table('rain_rates', function (Blueprint $table) {
            $table->unique(['ack_time']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
