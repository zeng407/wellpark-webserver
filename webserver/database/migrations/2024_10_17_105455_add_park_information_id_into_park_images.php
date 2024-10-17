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
        Schema::table('park_images', function (Blueprint $table) {
            $table->foreignId('park_information_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_images', function (Blueprint $table) {
            $table->dropForeign(['park_information_id']);
            $table->dropColumn('park_information_id');
        });
    }
};
