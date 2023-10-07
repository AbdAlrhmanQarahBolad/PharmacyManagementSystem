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
        Schema::create('warehouse_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('max_quantity')->default(0);
            //$table->unsignedBigInteger('price');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('medicine_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_medicines');
    }
};
