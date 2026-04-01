<?php
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = ['website_header_top_marquee', 'website_header_logo', 'website_header_search', 'website_header_user', 'website_header_hamburger', 'website_header_btn_text', 'website_header_btn_url', 'website_header_btn_bg_color', 'website_header_btn_text_color', 'website_header_sidebar_menu', 'website_home_settings', 'website_footer_settings'];

foreach ($columns as $col) {
    if (Schema::hasColumn('settings', $col)) {
        echo $col . " exists\n";
    } else {
        echo $col . " MISSING\n";
    }
}
