<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Removes pharmacy_id from medicine table to make medicines global (super admin only).
     * This should only be run AFTER data migration to pharmacy_inventory table.
     */
    public function up(): void
    {
        // Drop foreign key constraint first using DB statement for reliability
        // Try the known constraint name from the SQL dump
        try {
            DB::statement('ALTER TABLE medicine DROP FOREIGN KEY medicine_ibfk_1');
        } catch (\Exception $e) {
            // If that fails, try using Laravel's Schema builder
            Schema::table('medicine', function (Blueprint $table) {
                $table->dropForeign(['pharmacy_id']);
            });
        }
        
        Schema::table('medicine', function (Blueprint $table) {
            // Drop the index on pharmacy_id
            $table->dropIndex('fk_medicine_pharamacy_id');
        });
        
        Schema::table('medicine', function (Blueprint $table) {
            // Drop the column
            $table->dropColumn('pharmacy_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine', function (Blueprint $table) {
            // Add the column back
            $table->unsignedBigInteger('pharmacy_id')->after('form');
        });
        
        Schema::table('medicine', function (Blueprint $table) {
            // Add the index
            $table->index('pharmacy_id', 'fk_medicine_pharamacy_id');
            
            // Add the foreign key constraint
            $table->foreign('pharmacy_id', 'medicine_ibfk_1')
                ->references('id')
                ->on('pharmacy')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
