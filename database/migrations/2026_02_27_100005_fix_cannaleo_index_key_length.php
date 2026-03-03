<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MySQL utf8mb4: max key length 1000 bytes. String(255) = 1020 bytes -> too long.
     * Shorten indexed string columns to 191 so unique indexes work.
     */
    public function up(): void
    {
        if (Schema::hasTable('cannaleo_pharmacy')) {
            DB::statement('ALTER TABLE cannaleo_pharmacy MODIFY external_id VARCHAR(191) NOT NULL COMMENT "pharmacy_id from API"');
            // Add unique if not already present (e.g. migration failed before adding it)
            $indexes = DB::select("SHOW INDEX FROM cannaleo_pharmacy WHERE Key_name = 'cannaleo_pharmacy_external_id_unique'");
            if (empty($indexes)) {
                Schema::table('cannaleo_pharmacy', function (Blueprint $table) {
                    $table->unique('external_id');
                });
            }
        }

        if (Schema::hasTable('cannaleo_medicine')) {
            DB::statement('ALTER TABLE cannaleo_medicine MODIFY external_id VARCHAR(191) NOT NULL COMMENT "API id e.g. bedrocan-afina"');
        }
    }

    public function down(): void
    {
        // Reverting column length is optional; leave as 191 is safe
    }
};
