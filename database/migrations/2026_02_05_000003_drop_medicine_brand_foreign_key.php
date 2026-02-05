<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'medicine'
              AND COLUMN_NAME = 'brand_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE medicine DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: foreign key intentionally removed.
    }
};
