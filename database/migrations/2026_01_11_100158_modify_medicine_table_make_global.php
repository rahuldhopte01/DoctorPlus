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
     * NOTE: This migration adds strength and form columns if missing,
     * but does NOT remove pharmacy_id yet. That should be done after
     * data migration to pharmacy_inventory table.
     */
    public function up(): void
    {
        Schema::table('medicine', function (Blueprint $table) {
            // Add strength and form columns if they don't exist
            if (!Schema::hasColumn('medicine', 'strength')) {
                $table->string('strength', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('medicine', 'form')) {
                $table->string('form', 100)->nullable()->after('strength');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine', function (Blueprint $table) {
            if (Schema::hasColumn('medicine', 'form')) {
                $table->dropColumn('form');
            }
            if (Schema::hasColumn('medicine', 'strength')) {
                $table->dropColumn('strength');
            }
        });
    }
};
