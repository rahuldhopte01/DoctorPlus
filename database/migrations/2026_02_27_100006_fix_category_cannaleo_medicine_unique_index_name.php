<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MySQL identifier limit is 64 chars. Laravel's auto name was too long.
     */
    public function up(): void
    {
        if (! Schema::hasTable('category_cannaleo_medicine')) {
            return;
        }

        $indexes = DB::select("SHOW INDEX FROM category_cannaleo_medicine WHERE Key_name = 'cat_cannaleo_med_unique'");
        if (! empty($indexes)) {
            return;
        }

        // Drop auto-named unique if migration partially ran (e.g. different Laravel version generated it)
        $longName = 'category_cannaleo_medicine_category_id_cannaleo_medicine_id_unique';
        $longIndexes = DB::select("SHOW INDEX FROM category_cannaleo_medicine WHERE Key_name = ?", [$longName]);
        if (! empty($longIndexes)) {
            Schema::table('category_cannaleo_medicine', function (Blueprint $table) use ($longName) {
                $table->dropUnique($longName);
            });
        }

        Schema::table('category_cannaleo_medicine', function (Blueprint $table) {
            $table->unique(['category_id', 'cannaleo_medicine_id'], 'cat_cannaleo_med_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('category_cannaleo_medicine')) {
            return;
        }
        Schema::table('category_cannaleo_medicine', function (Blueprint $table) {
            $table->dropUnique('cat_cannaleo_med_unique');
        });
    }
};
