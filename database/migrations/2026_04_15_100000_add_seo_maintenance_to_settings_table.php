<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoMaintenanceToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('website_badge_settings');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('meta_keywords');
            $table->tinyInteger('maintenance_mode')->default(0)->after('og_image');
            $table->text('maintenance_message')->nullable()->after('maintenance_mode');
            $table->tinyInteger('google_indexing')->default(1)->after('maintenance_message');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'og_image', 'maintenance_mode', 'maintenance_message', 'google_indexing']);
        });
    }
}
