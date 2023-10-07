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
        Schema::create('pharmacy_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quantity')->default(0);
            //$table->unsignedBigInteger('net_price');
            //$table->unsignedBigInteger('commercial_price');
            $table->unsignedBigInteger('pharmacy_id');
            $table->unsignedBigInteger('medicine_id');
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_medicines');
    }
};
