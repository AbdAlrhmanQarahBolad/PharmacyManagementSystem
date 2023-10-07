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
        Schema::create('sale_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('price_of_quantity');
           // $table->unsignedBigInteger('commercialPrice');
            $table->unsignedBigInteger('sale_invoice_id');
            $table->foreign('sale_invoice_id')->references('id')->on('sale_invoices')->onDelete('cascade');
            $table->unsignedBigInteger('pharmacyMedicine_id');
            $table->foreign('pharmacyMedicine_id')->references('id')->on('pharmacy_medicines')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_details');
    }
};
