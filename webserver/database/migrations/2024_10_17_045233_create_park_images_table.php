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
        Schema::create('park_images', function (Blueprint $table) {
            $table->id();
            $table->string('park_no')->index();
            $table->string('path');
            $table->string('url');
            $table->timestamp('captured_at')->index();
            $table->string('recognition_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_images');
    }
};
