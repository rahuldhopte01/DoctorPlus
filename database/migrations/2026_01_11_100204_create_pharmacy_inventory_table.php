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
        Schema::create('pharmacy_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(0);
            $table->timestamps();
            
            $table->foreign('pharmacy_id')->references('id')->on('pharmacy')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicine')->onDelete('cascade');
            $table->index(['pharmacy_id', 'medicine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_inventory');
    }
};
