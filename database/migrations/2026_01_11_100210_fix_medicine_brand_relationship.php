<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if medicine_brand_medicine pivot table exists
        if (Schema::hasTable('medicine_brand_medicine')) {
            Schema::dropIfExists('medicine_brand_medicine');
        }
        
        // Add medicine_id to medicine_brands table if it doesn't exist
        if (!Schema::hasColumn('medicine_brands', 'medicine_id')) {
            Schema::table('medicine_brands', function (Blueprint $table) {
                $table->unsignedBigInteger('medicine_id')->nullable()->after('id');
                $table->foreign('medicine_id')->references('id')->on('medicine')->onDelete('cascade');
                $table->index('medicine_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('medicine_brands', 'medicine_id')) {
            Schema::table('medicine_brands', function (Blueprint $table) {
                $table->dropForeign(['medicine_id']);
                $table->dropColumn('medicine_id');
            });
        }
    }
};
