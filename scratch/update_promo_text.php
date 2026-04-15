<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$setting = App\Models\Setting::first();
$promo = json_decode($setting->website_header_promo_bar, true) ?: [];
$promo['text_italic'] = 'Erfrischen Sie im April Ihre Gesundheit:';
$promo['text_bold'] = 'Mit dem Rabattcode APRIL sparen Sie 10 €.';
$promo['text_bold_black'] = 'APRIL'; // Including this just in case
$promo['end_date'] = '2026-04-14T16:30';
$setting->website_header_promo_bar = json_encode($promo);
$setting->save();
echo "Updated Successfully";
