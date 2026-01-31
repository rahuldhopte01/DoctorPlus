<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot: categories have many medicines; medicines can belong to many categories.
     * Used to show category-specific medicines on questionnaire medicine selection.
     */
    public function up(): void
    {
        if (Schema::hasTable('category_medicine')) {
            return;
        }
        Schema::create('category_medicine', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('medicine_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicine')->onDelete('cascade');
            $table->unique(['category_id', 'medicine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_medicine');
    }
};
