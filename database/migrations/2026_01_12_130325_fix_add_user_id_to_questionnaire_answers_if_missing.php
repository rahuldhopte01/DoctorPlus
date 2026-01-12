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
        // Check if user_id column exists, if not, add all missing columns
        if (!Schema::hasColumn('questionnaire_answers', 'user_id')) {
            // First, check if we need to drop foreign key on appointment_id
            try {
                Schema::table('questionnaire_answers', function (Blueprint $table) {
                    $table->dropForeign(['appointment_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or have different name, continue
            }
            
            // Make appointment_id nullable using raw SQL
            try {
                DB::statement('ALTER TABLE questionnaire_answers MODIFY appointment_id BIGINT UNSIGNED NULL');
            } catch (\Exception $e) {
                // Column might already be nullable, continue
            }
            
            // Add new fields for immediate storage
            Schema::table('questionnaire_answers', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('appointment_id');
                $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
                $table->unsignedBigInteger('questionnaire_id')->nullable()->after('category_id');
                $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending')->after('questionnaire_id');
                $table->timestamp('submitted_at')->nullable()->after('status');
            });
            
            // Re-add foreign key for appointment_id (now nullable)
            try {
                Schema::table('questionnaire_answers', function (Blueprint $table) {
                    $table->foreign('appointment_id')
                        ->references('id')
                        ->on('appointment')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
            
            // Add foreign key constraints for new fields
            Schema::table('questionnaire_answers', function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                    
                $table->foreign('category_id')
                    ->references('id')
                    ->on('category')
                    ->onDelete('cascade');
                    
                $table->foreign('questionnaire_id')
                    ->references('id')
                    ->on('questionnaires')
                    ->onDelete('cascade');
            });
            
            // Add indexes
            Schema::table('questionnaire_answers', function (Blueprint $table) {
                $table->index(['user_id', 'category_id', 'questionnaire_id', 'status'], 'qa_user_cat_quest_status_idx');
                $table->index(['user_id', 'status'], 'qa_user_status_idx');
                $table->index(['category_id', 'status'], 'qa_category_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only adds columns if they don't exist
        // Down migration is not needed as the original migration handles rollback
    }
};
