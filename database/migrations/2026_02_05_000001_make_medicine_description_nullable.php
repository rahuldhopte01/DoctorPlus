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
        $column = DB::selectOne("
            SELECT COLUMN_TYPE, IS_NULLABLE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'medicine'
              AND COLUMN_NAME = 'description'
        ");

        if ($column && strtoupper($column->IS_NULLABLE) !== 'YES') {
            $type = $column->COLUMN_TYPE;
            DB::statement("ALTER TABLE medicine MODIFY description {$type} NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $column = DB::selectOne("
            SELECT COLUMN_TYPE, IS_NULLABLE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'medicine'
              AND COLUMN_NAME = 'description'
        ");

        if ($column && strtoupper($column->IS_NULLABLE) === 'YES') {
            DB::statement("UPDATE medicine SET description = '' WHERE description IS NULL");
            $type = $column->COLUMN_TYPE;
            DB::statement("ALTER TABLE medicine MODIFY description {$type} NOT NULL");
        }
    }
};
