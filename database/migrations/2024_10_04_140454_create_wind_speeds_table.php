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
        Schema::create('winds', function (Blueprint $table) {
            $table->id();
            $table->float('value');
            $table->integer('direction');
            $table->dateTime('ack_time');
            $table->boolean('sync');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wind_speeds');
    }
};
