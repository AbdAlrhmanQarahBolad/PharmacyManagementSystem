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
        Schema::create('med_mats', function (Blueprint $table) {
            $table->id();
            $table->integer('concentration');
            $table->unsignedBigInteger('med_id');
            $table->unsignedBigInteger('active_id');
            $table->foreign('med_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('active_id')->references('id')->on('active_mats')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_mats');
    }
};
