<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'website_header_top_marquee')) $table->text('website_header_top_marquee')->nullable();
            if (!Schema::hasColumn('settings', 'website_header_logo')) $table->text('website_header_logo')->nullable();
            if (!Schema::hasColumn('settings', 'website_header_search')) $table->tinyInteger('website_header_search')->default(1);
            if (!Schema::hasColumn('settings', 'website_header_user')) $table->tinyInteger('website_header_user')->default(1);
            if (!Schema::hasColumn('settings', 'website_header_hamburger')) $table->tinyInteger('website_header_hamburger')->default(1);
            if (!Schema::hasColumn('settings', 'website_header_btn_text')) $table->text('website_header_btn_text')->nullable();
            if (!Schema::hasColumn('settings', 'website_header_btn_url')) $table->text('website_header_btn_url')->nullable();
            if (!Schema::hasColumn('settings', 'website_header_btn_bg_color')) $table->string('website_header_btn_bg_color', 20)->nullable();
            if (!Schema::hasColumn('settings', 'website_header_btn_text_color')) $table->string('website_header_btn_text_color', 20)->nullable();
            if (!Schema::hasColumn('settings', 'website_header_sidebar_menu')) $table->text('website_header_sidebar_menu')->nullable();
            if (!Schema::hasColumn('settings', 'website_home_settings')) $table->text('website_home_settings')->nullable();
            if (!Schema::hasColumn('settings', 'website_footer_settings')) $table->text('website_footer_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'website_header_top_marquee',
                'website_header_logo',
                'website_header_search',
                'website_header_user',
                'website_header_hamburger',
                'website_header_btn_text',
                'website_header_btn_url',
                'website_header_btn_bg_color',
                'website_header_btn_text_color',
                'website_header_sidebar_menu',
                'website_home_settings',
                'website_footer_settings',
            ]);
        });
    }
};
