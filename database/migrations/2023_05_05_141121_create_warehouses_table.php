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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('warehouseName')->unique();
            $table->string('number')->nullable();
            $table->string('path_of_photo');
            //$table->decimal('longitude', $precision = 20, $scale = 3)->nullable();
            //$table->decimal('latitude', $precision = 20, $scale = 3)->nullable();
            $table->string('owner_of_permission_name')->nullable();
            $table->boolean('validated')->default(0);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
