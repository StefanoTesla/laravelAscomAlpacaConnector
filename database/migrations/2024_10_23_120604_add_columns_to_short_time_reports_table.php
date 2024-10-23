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
        Schema::table('short_time_reports', function (Blueprint $table) {
            $table->after('wind_dir', function($table){
                $table->float('sky_temperature',2)->nullable();
                $table->float('sky_brightness',2)->nullable();
                $table->float('sqm',2)->nullable();
                $table->integer('cloud_cover')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_time_reports', function (Blueprint $table) {

        });
    }
};
