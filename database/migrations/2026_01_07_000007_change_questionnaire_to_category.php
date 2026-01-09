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
        // Check if category_id already exists
        $columns = DB::select("SHOW COLUMNS FROM questionnaires LIKE 'category_id'");
        if (!empty($columns)) {
            // Column already exists, just ensure foreign key is set
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'questionnaires' 
                AND CONSTRAINT_NAME = 'questionnaires_category_id_foreign'
            ");
            
            if (empty($fkExists)) {
                Schema::table('questionnaires', function (Blueprint $table) {
                    $table->foreign('category_id')
                        ->references('id')
                        ->on('category')
                        ->onDelete('cascade');
                });
            }
            return; // Already migrated
        }
        
        // Check if treatment_id exists and drop it
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'questionnaires' 
            AND CONSTRAINT_NAME = 'questionnaires_treatment_id_foreign'
        ");
        
        if (!empty($fkExists)) {
            Schema::table('questionnaires', function (Blueprint $table) {
                $table->dropForeign(['treatment_id']);
            });
        }
        
        // Delete existing questionnaires (since we're changing the relationship structure)
        DB::table('questionnaires')->delete();
        
        // Drop the treatment_id column if it exists
        $columns = DB::select("SHOW COLUMNS FROM questionnaires LIKE 'treatment_id'");
        if (!empty($columns)) {
            Schema::table('questionnaires', function (Blueprint $table) {
                $table->dropColumn('treatment_id');
            });
        }
        
        // Add category_id column
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->after('id');
            $table->index('category_id');
        });
        
        // Add foreign key constraint
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            // Drop category foreign key
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn('category_id');
            
            // Restore treatment_id
            $table->unsignedBigInteger('treatment_id')->after('id');
            $table->index('treatment_id');
        });
        
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->foreign('treatment_id')
                ->references('id')
                ->on('treatments')
                ->onDelete('cascade');
        });
    }
};
