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
        Schema::create('predict_park_informations', function (Blueprint $table) {
            $table->id();
            $table->string('park_no')->index();
            $table->integer('free_quantity');
            $table->integer('free_quantity_big');
            $table->integer('free_quantity_mot');
            $table->integer('free_quantity_dis');
            $table->integer('free_quantity_cw');
            $table->integer('free_quantity_ecar');
            $table->timestamp('future_time')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predict_park_informations');
    }
};
