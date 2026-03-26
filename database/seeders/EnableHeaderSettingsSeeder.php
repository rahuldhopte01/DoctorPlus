<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class EnableHeaderSettingsSeeder extends Seeder
{
    public function run()
    {
        $s = Setting::first();
        if ($s) {
            $s->website_header_search = 1;
            $s->website_header_user = 1;
            $s->website_header_hamburger = 1;
            $s->website_header_btn_text = 'Jetzt Rezept anfragen';
            $s->website_header_btn_url = '/categories';
            $s->website_header_btn_bg_color = '#7b42f6';
            $s->website_header_btn_text_color = '#ffffff';
            $s->website_header_top_marquee = json_encode([
                ['text' => 'Trusted Shop - Seit 2004', 'icon' => ''],
                ['text' => 'DHL Versand - Schnell & Sicher', 'icon' => ''],
                ['text' => 'EU-registrierte Ärzte', 'icon' => '']
            ]);
            $s->save();
        }
    }
}
