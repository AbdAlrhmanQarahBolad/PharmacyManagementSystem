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
        Schema::create('buy_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('total_price');
            $table->date('date_of_invoice') ;
            $table->boolean('state');
            $table->unsignedBigInteger('warehouseDispenser_id');
            $table->foreign('warehouseDispenser_id')->references('id')->on('warehouse_dispensers')->onDelete('cascade');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('buy_orders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_invoices');
    }
};
