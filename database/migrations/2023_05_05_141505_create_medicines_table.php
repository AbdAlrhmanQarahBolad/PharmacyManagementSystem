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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->nullable();
            $table->string('trade_name_ar')->nullable();
            $table->string('trade_name_en')->nullable();
            $table->string('description_en')->nullable();
            $table->string('description_ar')->nullable();
            $table->string('medicine_form_ar')->nullable();
            $table->string('medicine_form_en')->nullable();
           // $table->string('company_name')->nullable();
            $table->unsignedBigInteger('commercial_price')->nullable();
           // $table->string('commercial_price')->nullable();
            $table->unsignedBigInteger('net_price')->nullable();
           // $table->string('net_price')->nullable();
           // $table->unsignedBigInteger('size')->nullable();
            $table->string('size')->nullable();
            $table->unsignedBigInteger('parts')->nullable();
           // $table->string('parts')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
           // $table->string('company_id')->nullable();
            $table->string('medicine_photo_path')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
