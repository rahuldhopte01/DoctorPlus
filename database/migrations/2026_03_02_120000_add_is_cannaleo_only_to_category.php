<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cannaleo-only categories: no delivery choice; after submit go straight to Cannaleo pharmacy → medicine.
     */
    public function up(): void
    {
        Schema::table('category', function (Blueprint $table) {
            $table->boolean('is_cannaleo_only')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropColumn('is_cannaleo_only');
        });
    }
};
