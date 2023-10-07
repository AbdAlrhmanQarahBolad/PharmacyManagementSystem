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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('additional');
            $table->unsignedBigInteger('price');
           // $table->unsignedBigInteger('commercialPrice');
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('buy_invoices')->onDelete('cascade');
            $table->unsignedBigInteger('warehouseMedicine_id');
            $table->foreign('warehouseMedicine_id')->references('id')->on('warehouse_medicines')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
