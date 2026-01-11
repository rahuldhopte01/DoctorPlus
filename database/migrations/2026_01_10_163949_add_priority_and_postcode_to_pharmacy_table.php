<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pharmacy', function (Blueprint $table) {
            $table->string('postcode')->nullable()->after('address');
            $table->boolean('is_priority')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy', function (Blueprint $table) {
            $table->dropColumn(['postcode', 'is_priority']);
        });
    }
};
