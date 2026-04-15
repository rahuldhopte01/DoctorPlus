<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$settings = App\Models\Setting::first()->website_header_promo_bar;
file_put_contents('scratch/promo_settings.json', $settings);
