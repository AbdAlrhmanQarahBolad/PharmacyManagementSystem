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
        Schema::create('warehousemedicines_loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_quantity');
            $table->unsignedBigInteger('warehousemedicine_id')->nullable();
            $table->foreign('warehousemedicine_id')->references('id')->on('warehouse_medicines')->onDelete('cascade');
            $table->unsignedBigInteger('load_id')->nullable();
            $table->foreign('load_id')->references('id')->on('warehouse_medicines')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehousemedicines_loads');
    }
};
