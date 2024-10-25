<?php

use App\Enums\SyncStatusEnum;
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
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('humidities', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('pressures', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('winds', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('wind_gusts', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('dew_points', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('rain_rates', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('cloud_covers', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('sky_brightnesses', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('sky_temperatures', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('sky_qualities', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
        });
        Schema::table('rain_rates', function (Blueprint $table) {
            $table->dropColumn('sync');
            $table->integer('sync')->default(SyncStatusEnum::INCOMPLETE);
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
