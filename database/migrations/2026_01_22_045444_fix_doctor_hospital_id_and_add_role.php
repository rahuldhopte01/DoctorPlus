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
     * Fixes hospital_id to be a proper foreign key (single hospital per doctor)
     * Adds doctor_role field (ADMIN_DOCTOR or SUB_DOCTOR)
     */
    public function up(): void
    {
        // First, handle existing data - if hospital_id contains comma-separated values, take the first one
        DB::statement("
            UPDATE doctor 
            SET hospital_id = SUBSTRING_INDEX(hospital_id, ',', 1)
            WHERE hospital_id IS NOT NULL 
            AND hospital_id != '' 
            AND hospital_id LIKE '%,%'
        ");
        
        // Convert empty strings to NULL
        DB::statement("
            UPDATE doctor 
            SET hospital_id = NULL 
            WHERE hospital_id = '' OR hospital_id = '0'
        ");
        
        // Drop existing index if it exists
        try {
            Schema::table('doctor', function (Blueprint $table) {
                $table->dropIndex('fk_hospital_id');
            });
        } catch (\Exception $e) {
            // Index might not exist or have different name
        }
        
        // Change hospital_id from varchar to unsignedBigInteger
        // Using raw SQL because Laravel's change() requires doctrine/dbal
        DB::statement('ALTER TABLE doctor MODIFY hospital_id BIGINT UNSIGNED NULL');
        
        // Add foreign key constraint
        Schema::table('doctor', function (Blueprint $table) {
            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospital')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        
        // Add doctor_role field
        Schema::table('doctor', function (Blueprint $table) {
            $table->enum('doctor_role', ['ADMIN_DOCTOR', 'SUB_DOCTOR'])
                ->default('SUB_DOCTOR')
                ->after('hospital_id');
        });
        
        // Add index for doctor_role
        Schema::table('doctor', function (Blueprint $table) {
            $table->index('doctor_role', 'idx_doctor_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop doctor_role field
        Schema::table('doctor', function (Blueprint $table) {
            $table->dropIndex('idx_doctor_role');
            $table->dropColumn('doctor_role');
        });
        
        // Drop foreign key constraint
        Schema::table('doctor', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });
        
        // Change hospital_id back to varchar(255)
        DB::statement('ALTER TABLE doctor MODIFY hospital_id VARCHAR(255) NULL');
        
        // Re-add index
        Schema::table('doctor', function (Blueprint $table) {
            $table->index('hospital_id', 'fk_hospital_id');
        });
    }
};
