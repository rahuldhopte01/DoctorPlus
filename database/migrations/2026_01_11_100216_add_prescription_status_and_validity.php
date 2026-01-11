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
        Schema::table('prescription', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription', 'status')) {
                $table->enum('status', ['approved', 'approved_pending_payment', 'active', 'expired'])->default('approved')->after('user_id');
            }
            if (!Schema::hasColumn('prescription', 'valid_from')) {
                $table->dateTime('valid_from')->nullable()->after('status');
            }
            if (!Schema::hasColumn('prescription', 'valid_until')) {
                $table->dateTime('valid_until')->nullable()->after('valid_from');
            }
            if (!Schema::hasColumn('prescription', 'validity_days')) {
                $table->integer('validity_days')->nullable()->after('valid_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            if (Schema::hasColumn('prescription', 'validity_days')) {
                $table->dropColumn('validity_days');
            }
            if (Schema::hasColumn('prescription', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
            if (Schema::hasColumn('prescription', 'valid_from')) {
                $table->dropColumn('valid_from');
            }
            if (Schema::hasColumn('prescription', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
