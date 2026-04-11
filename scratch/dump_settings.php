<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$settings = App\Models\Setting::first()->website_home_settings;
file_put_contents('scratch/home_settings.json', $settings);
