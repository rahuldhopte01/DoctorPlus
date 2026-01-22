<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrates existing treatment_id and category_id data to pivot tables,
     * then removes the columns from doctor table.
     */
    public function up(): void
    {
        // Migrate existing treatment_id data to doctor_treatment pivot table
        DB::statement('
            INSERT INTO doctor_treatment (doctor_id, treatment_id, created_at, updated_at)
            SELECT id, treatment_id, NOW(), NOW()
            FROM doctor
            WHERE treatment_id IS NOT NULL
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ');

        // Migrate existing category_id data to doctor_category pivot table
        DB::statement('
            INSERT INTO doctor_category (doctor_id, category_id, created_at, updated_at)
            SELECT id, category_id, NOW(), NOW()
            FROM doctor
            WHERE category_id IS NOT NULL
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ');

        // Drop foreign key constraints - query for actual constraint names
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'doctor' 
            AND COLUMN_NAME IN ('treatment_id', 'category_id')
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($constraints as $constraint) {
            try {
                DB::statement("ALTER TABLE doctor DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Constraint might not exist or already dropped
            }
        }

        // Also try common constraint names as fallback (including the one from error message)
        $commonNames = [
            'fk_category_id',
            'fk_treatment_id',
            'doctor_ibfk_1',
            'doctor_ibfk_2',
            'doctor_treatment_id_foreign',
            'doctor_category_id_foreign',
            'doctor_ibfk_treatment_id',
            'doctor_ibfk_category_id'
        ];

        foreach ($commonNames as $constraintName) {
            try {
                DB::statement("ALTER TABLE doctor DROP FOREIGN KEY `{$constraintName}`");
            } catch (\Exception $e) {
                // Constraint doesn't exist with this name, continue
            }
        }

        // Drop indexes on these columns if they exist
        $indexes = DB::select("
            SELECT DISTINCT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'doctor' 
            AND COLUMN_NAME IN ('treatment_id', 'category_id')
            AND INDEX_NAME != 'PRIMARY'
        ");

        foreach ($indexes as $index) {
            try {
                DB::statement("ALTER TABLE doctor DROP INDEX `{$index->INDEX_NAME}`");
            } catch (\Exception $e) {
                // Index might not exist or already dropped
            }
        }

        // Remove columns from doctor table
        Schema::table('doctor', function (Blueprint $table) {
            $table->dropColumn(['treatment_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add columns back to doctor table
        Schema::table('doctor', function (Blueprint $table) {
            $table->unsignedBigInteger('treatment_id')->nullable()->after('id');
            $table->unsignedBigInteger('category_id')->nullable()->after('treatment_id');
        });

        // Migrate data back from pivot tables (take first treatment/category for each doctor)
        DB::statement('
            UPDATE doctor d
            INNER JOIN (
                SELECT doctor_id, treatment_id
                FROM doctor_treatment
                GROUP BY doctor_id
            ) dt ON d.id = dt.doctor_id
            SET d.treatment_id = dt.treatment_id
        ');

        DB::statement('
            UPDATE doctor d
            INNER JOIN (
                SELECT doctor_id, category_id
                FROM doctor_category
                GROUP BY doctor_id
            ) dc ON d.id = dc.doctor_id
            SET d.category_id = dc.category_id
        ');

        // Add foreign key constraints back
        Schema::table('doctor', function (Blueprint $table) {
            $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('set null');
        });
    }
};
