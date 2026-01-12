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
        // Drop foreign key constraint if it exists
        try {
            Schema::table('prescription', function (Blueprint $table) {
                $table->dropForeign(['appointment_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist or have different name, continue
        }
        
        // Make appointment_id nullable using raw SQL
        DB::statement('ALTER TABLE prescription MODIFY appointment_id BIGINT UNSIGNED NULL');
        
        // Re-add foreign key for appointment_id (now nullable)
        try {
            Schema::table('prescription', function (Blueprint $table) {
                $table->foreign('appointment_id')
                    ->references('id')
                    ->on('appointment')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        try {
            Schema::table('prescription', function (Blueprint $table) {
                $table->dropForeign(['appointment_id']);
            });
        } catch (\Exception $e) {
            // Continue
        }
        
        // Make appointment_id required again using raw SQL
        DB::statement('ALTER TABLE prescription MODIFY appointment_id BIGINT UNSIGNED NOT NULL');
        
        // Re-add foreign key
        Schema::table('prescription', function (Blueprint $table) {
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointment')
                ->onDelete('cascade');
        });
    }
};
