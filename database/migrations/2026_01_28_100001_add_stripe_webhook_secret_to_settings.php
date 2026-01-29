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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'stripe_webhook_secret')) {
                $table->string('stripe_webhook_secret')->nullable()->after('stripe_secret_key');
            }
            if (!Schema::hasColumn('settings', 'prescription_fee')) {
                $table->decimal('prescription_fee', 10, 2)->nullable()->default(50.00)->after('stripe_webhook_secret');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'stripe_webhook_secret')) {
                $table->dropColumn('stripe_webhook_secret');
            }
            if (Schema::hasColumn('settings', 'prescription_fee')) {
                $table->dropColumn('prescription_fee');
            }
        });
    }
};
