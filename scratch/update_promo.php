<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$setting = App\Models\Setting::first();
$promo = json_decode($setting->website_header_promo_bar, true) ?: [];
$promo['end_date'] = '2026-04-14T16:30';
$setting->website_header_promo_bar = json_encode($promo);
$setting->save();
echo "Updated Successfully";
