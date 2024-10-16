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
        Schema::create('park_informations', function (Blueprint $table) {
            $table->id();
            $table->string('park_no')->index();
            $table->string('parking_name');
            $table->string('address');
            $table->string('business_hours');
            $table->string('weekdays');
            $table->text('holiday');
            $table->integer('free_quantity_big');
            $table->integer('total_quantity_big');
            $table->integer('free_quantity');
            $table->integer('total_quantity');
            $table->integer('free_quantity_mot');
            $table->integer('total_quantity_mot');
            $table->integer('free_quantity_dis');
            $table->integer('total_quantity_dis');
            $table->integer('free_quantity_cw');
            $table->integer('total_quantity_cw');
            $table->integer('free_quantity_ecar');
            $table->integer('total_quantity_ecar');
            $table->decimal('longitude', 10, 6)->index();;
            $table->decimal('latitude', 10, 6)->index();;
            $table->timestamp('update_time', 3)->index();;
            $table->timestamps();
        });

        Schema::create('latest_park_informations', function (Blueprint $table) {
            $table->id();
            $table->string('park_no')->index();
            $table->foreignId('park_information_id')->constrained();
            $table->timestamp('update_time', 3)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest_park_informations');
        Schema::dropIfExists('park_informations');
    }
};
