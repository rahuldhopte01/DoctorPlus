<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot: assign Cannaleo medicines to our questionnaire categories (same idea as category_medicine).
     */
    public function up(): void
    {
        Schema::create('category_cannaleo_medicine', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('cannaleo_medicine_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('cannaleo_medicine_id')->references('id')->on('cannaleo_medicine')->onDelete('cascade');
            $table->unique(['category_id', 'cannaleo_medicine_id'], 'cat_cannaleo_med_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_cannaleo_medicine');
    }
};
