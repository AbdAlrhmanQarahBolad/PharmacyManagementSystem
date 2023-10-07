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
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('pharmacyName');
            $table->string('number')->unique();
            $table->string('path_of_photo');
            $table->decimal('longitude', $precision = 20, $scale = 4)->nullable();
            $table->decimal('latitude', $precision = 20, $scale = 4)->nullable();
            $table->string('owner_of_permission_name')->nullable();
            $table->boolean('validated')->default(0);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            $table->unsignedBigInteger('holiday_id');
            $table->foreign('holiday_id')->references('id')->on('week_days')->onDelete('cascade');
            $table->integer('from_min');
            $table->integer('to_min');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
