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
        Schema::create('sky_qualities', function (Blueprint $table) {
            $table->id();
            $table->float('value',2);
            $table->dateTime('ack_time');
            $table->boolean('sync');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sky_qualities');
    }
};
