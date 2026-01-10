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
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('cascade');
            $table->foreignId('medicine_brand_id')->constrained('medicine_brands')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10)->comment('Threshold for low stock notification');
            $table->enum('stock_status', ['in_stock', 'low_stock', 'out_of_stock'])->default('out_of_stock');
            $table->timestamps();
            
            $table->unique(['pharmacy_id', 'medicine_id', 'medicine_brand_id'], 'pharmacy_medicine_brand_unique');
            $table->index(['pharmacy_id', 'stock_status']);
            $table->index('stock_status');
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
