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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quantity');
            //$table->unsignedBigInteger('received_amounts');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('buy_orders')->onDelete('cascade');
            $table->unsignedBigInteger('warehouseMedicine_id');
            $table->foreign('warehouseMedicine_id')->references('id')->on('warehouse_medicines')->onDelete('cascade');
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->unsignedBigInteger('load_id')->nullable();
            $table->foreign('load_id')->references('id')->on('warehousemedicines_loads')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
