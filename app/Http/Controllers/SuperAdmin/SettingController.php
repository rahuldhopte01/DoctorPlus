<?php

namespace App\Http\Controllers\SuperAdmin;

use App;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Setting;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use LicenseBoxExternalAPI;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    public function setting()
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $setting = Setting::first();
        $timezones = Timezone::get();
        $currencies = Currency::get();
        $languages = Language::whereStatus(1)->get();

        return view('superAdmin.setting.setting', compact('setting', 'timezones', 'currencies', 'languages'));
    }

    public function update_general_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate(
            [
                'email' => 'bail|email',
                'contact' => 'bail|digits_between:6,12',
                'company_white_logo' => 'bail|max:1000',
                'company_logo' => 'bail|mimes:jpeg,png,jpg|max:1000',
                'company_favicon' => 'bail|mimes:jpeg,png,jpg|max:1000',
            ],
            [
                'company_white_logo.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ],
            [
                'company_logo.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ],
            [
                'company_favicon.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ],
        );
        $setting = Setting::first();
        $data = $request->all();
        $currency = Currency::where('id', $data['currency_code'])->first();
        $data['currency_symbol'] = $currency->symbol;
        $data['currency_id'] = $data['currency_code'];
        $data['currency_code'] = $currency->code;
        // $data['currency_symbol'] = Currency::where('code',$data['currency_code'])->first()->symbol;
        if ($request->hasFile('company_white_logo')) {
            (new CustomController)->deleteFile($setting->company_white_logo);
            $data['company_white_logo'] = (new CustomController)->imageUpload($request->company_white_logo);
        }
        if ($request->hasFile('company_logo')) {
            (new CustomController)->deleteFile($setting->company_logo);
            $data['company_logo'] = (new CustomController)->imageUpload($request->company_logo);
        }
        if ($request->hasFile('company_favicon')) {
            (new CustomController)->deleteFile($setting->company_favicon);
            $data['company_favicon'] = (new CustomController)->imageUpload($request->company_favicon);
        }
        $data['cancel_reason'] = json_encode($data['cancel_reason']);
        $setting->update($data);
        $this->changeLanguage();
        $timezone['timezone'] = $data['timezone'];
        $success = (new CustomController)->updateENV($timezone);
        if ($success) {
            return redirect()->back()->withStatus(__('General Setting updated successfully..!!'));
        } else {
            return redirect()->back()->with('error_msg', __('Env file has not enough permission.'));
        }
    }

    public function changeLanguage()
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $language = Setting::first()->language;
        App::setLocale($language);
        session()->put('locale', $language);
        $direction = Language::where('name', $language)->first()->direction;
        session()->put('direction', $direction);

        return true;
    }

    public function update_payment_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'stripe_public_key' => 'bail|required_if:stripe,1',
            'stripe_secret_key' => 'bail|required_if:stripe,1',
            'paypal_secret_key' => 'bail|required_if:paypal,1',
            'paypal_client_id' => 'bail|required_if:paypal,1',
            'razor_key' => 'bail|required_if:razor,1',
            'paystack_public_key' => 'bail|required_if:paystack,1',
            'flutterwave_key' => 'bail|required_if:flutterwave,1',
        ]);
        $data = $request->all();
        $data['stripe'] = $request->has('stripe') ? 1 : 0;
        $data['cod'] = $request->has('cod') ? 1 : 0;
        $data['paypal'] = $request->has('paypal') ? 1 : 0;
        $data['razor'] = $request->has('razor') ? 1 : 0;
        $data['flutterwave'] = $request->has('flutterwave') ? 1 : 0;
        $data['paystack'] = $request->has('paystack') ? 1 : 0;
        $id = Setting::first();
        $id->update($data);

        return redirect()->back()->withStatus(__('Payment Setting updated successfully..!!'));
    }

    /**
     * Test that the stored Stripe credentials (from Payment setting) are valid.
     * Uses the same Setting::first() used by all payment flows.
     */
    public function testStripeConnection(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $setting = Setting::first();
        $secretKey = $setting->stripe_secret_key ?? null;
        if (empty($secretKey)) {
            return response()->json(['success' => false, 'message' => __('Stripe secret key is not configured.')]);
        }
        try {
            $stripe = new StripeClient($secretKey);
            $stripe->balance->retrieve();
            return response()->json([
                'success' => true,
                'message' => __('Stripe credentials are valid. This key is used for all Stripe payments in the system.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Stripe connection failed: ') . $e->getMessage(),
            ]);
        }
    }

    public function update_verification_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            // 'verification' => 'bail|required|in:0,1',
            'verification_method' => 'bail|required_if:verification,1|in:email,sms',
            'twilio_auth_token' => 'bail|required_if:using_msg,1',
            'twilio_acc_id' => 'bail|required_if:using_msg,1',
            'twilio_phone_no' => 'bail|required_if:using_msg,1',
            'mail_mailer' => 'bail|required_if:using_mail,1',
            'mail_host' => 'bail|required_if:using_mail,1',
            'mail_port' => 'bail|required_if:using_mail,1',
            'mail_username' => 'bail|required_if:using_mail,1',
            'mail_password' => 'bail|required_if:using_mail,1',
            'mail_encryption' => 'bail|required_if:using_mail,1',
            'mail_from_address' => 'bail|required_if:using_mail,1',
            'mail_from_name' => 'bail|required_if:using_mail,1',
        ],
            [
                'verification_method.required_if' => 'Choose either email or sms verification method',
            ]
        );
        $data = $request->all();

        if (isset($data['verification']) && $data['verification_method'] == 'email') {
            $data['using_mail'] = 1;
            $data['using_msg'] = 0;
        } elseif (isset($data['verification']) && $data['verification_method'] == 'sms') {
            $data['using_mail'] = 0;
            $data['using_msg'] = 1;
        } else {
            $data['using_mail'] = 0;
            $data['using_msg'] = 0;
            $data['verification'] = 0;
        }

        Setting::find(1)->update($data);

        return redirect()->back()->with('status', __('verification Setting updated successfully..!!'));
    }

    public function update_content(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $data = $request->all();
        $setting = Setting::first();
        
        if ($request->hasFile('banner_image')) {
            (new CustomController)->deleteFile($setting->banner_image);
            $data['banner_image'] = (new CustomController)->imageUpload($request->banner_image);
        }
        if ($request->hasFile('landing_popup_image')) {
            (new CustomController)->deleteFile($setting->landing_popup_image);
            $data['landing_popup_image'] = (new CustomController)->imageUpload($request->landing_popup_image);
        }

        // Header Logo
        if ($request->hasFile('website_header_logo')) {
            (new CustomController)->deleteFile($setting->website_header_logo);
            $data['website_header_logo'] = (new CustomController)->imageUpload($request->website_header_logo);
        }

        // Handle Top Marquee (JSON)
        if ($request->has('marquee_text')) {
            $marquees = [];
            foreach ($request->marquee_text as $index => $text) {
                if (!empty($text)) {
                    $icon = $request->marquee_icon_current[$index] ?? null;
                    if ($request->hasFile("marquee_icon.$index")) {
                        if ($icon) (new CustomController)->deleteFile($icon);
                        $icon = (new CustomController)->imageUpload($request->file("marquee_icon.$index"));
                    }
                    $marquees[] = [
                        'text' => $text,
                        'icon' => $icon
                    ];
                }
            }
            $data['website_header_top_marquee'] = json_encode($marquees);
        }

        // Handle Sidebar Menu (JSON)
        if ($request->has('menu_label')) {
            $menu = [];
            foreach ($request->menu_label as $index => $label) {
                if (!empty($label)) {
                    $menu[] = [
                        'label' => $label,
                        'url' => $request->menu_url[$index] ?? '#'
                    ];
                }
            }
            $data['website_header_sidebar_menu'] = json_encode($menu);
        }

        // Handle Promo Bar (JSON)
        if ($request->has('promo_text_italic') || $request->has('promo_status')) {
            $promoInfo = [
                'status' => $request->has('promo_status') ? 1 : 0,
                'text_italic' => $request->promo_text_italic,
                'text_bold' => $request->promo_text_bold,
                'end_date' => $request->promo_end_date,
            ];
            $data['website_header_promo_bar'] = json_encode($promoInfo);
        }

        // Handle Home Page Settings (Hero, How it Works, About)
        $home_settings = json_decode($setting->website_home_settings ?? '{}', true);
        
        // Hero Section
        if ($request->has('hero_title')) {
            // Process Trust Items
            $trustItems = [];
            foreach ($request->hero_trust_text ?? [] as $index => $text) {
                if (!empty($text)) {
                    $trustItems[] = [
                        'text' => $text,
                        'icon_class' => $request->hero_trust_icon_class[$index] ?? '',
                    ];
                }
            }

            // Process Quick Links
            $quickLinks = [];
            foreach ($request->hero_quick_link_title ?? [] as $index => $title) {
                if (!empty($title)) {
                    $image = $request->hero_quick_link_image_current[$index] ?? null;
                    if ($request->hasFile("hero_quick_link_image.$index")) {
                        if ($image) (new CustomController)->deleteFile($image);
                        $image = (new CustomController)->imageUpload($request->file("hero_quick_link_image.$index"));
                    }
                    $quickLinks[] = [
                        'title' => $title,
                        'subtitle' => $request->hero_quick_link_subtitle[$index] ?? '',
                        'badge' => $request->hero_quick_link_badge[$index] ?? '',
                        'url' => $request->hero_quick_link_url[$index] ?? '#',
                        'icon_class' => $request->hero_quick_link_icon_class[$index] ?? '',
                        'image' => $image,
                    ];
                }
            }

            $home_settings['hero'] = [
                'badge' => $request->hero_badge,
                'title' => $request->hero_title,
                'typing_keywords' => $request->hero_typing_keywords,
                'description' => $request->hero_description,
                'btn_text' => $request->hero_btn_text,
                'btn_url' => $request->hero_btn_url,
                'rating_stars' => $request->hero_rating_stars,
                'rating_score' => $request->hero_rating_score,
                'rating_text' => $request->hero_rating_text,
                'live_viewers' => $request->hero_live_viewers,
                'bg_color' => $request->hero_bg_color ?? '#f3ecff',
                'trust_items' => $trustItems,
                'quick_links' => $quickLinks,
                'image' => $home_settings['hero']['image'] ?? null,
                'bg_image' => $home_settings['hero']['bg_image'] ?? null,
            ];
            
            if ($request->hasFile('hero_image')) {
                if (!empty($home_settings['hero']['image'])) (new CustomController)->deleteFile($home_settings['hero']['image']);
                $home_settings['hero']['image'] = (new CustomController)->imageUpload($request->hero_image);
            }

            if ($request->hasFile('hero_bg_image')) {
                if (!empty($home_settings['hero']['bg_image'])) (new CustomController)->deleteFile($home_settings['hero']['bg_image']);
                $home_settings['hero']['bg_image'] = (new CustomController)->imageUpload($request->hero_bg_image);
            }
        }

        // How it Works
        if ($request->has('how_it_works_title')) {
            $steps = [];
            foreach ($request->step_title ?? [] as $index => $title) {
                $icon = $request->step_icon_current[$index] ?? null;
                if ($request->hasFile("step_icon.$index")) {
                    if ($icon) (new CustomController)->deleteFile($icon);
                    $icon = (new CustomController)->imageUpload($request->file("step_icon.$index"));
                }
                $steps[] = [
                    'title' => $title,
                    'text' => $request->step_text[$index] ?? '',
                    'icon' => $icon
                ];
            }
            $home_settings['how_it_works'] = [
                'title' => $request->how_it_works_title,
                'subtitle' => $request->how_it_works_subtitle,
                'badge' => $request->how_it_works_badge,
                'steps' => $steps
            ];
        }

        // About Section
        if ($request->has('about_title')) {
            $home_settings['about'] = [
                'badge' => $request->about_badge,
                'title' => $request->about_title,
                'description' => $request->about_description,
                'features' => $request->about_features ?: [],
                'image' => $home_settings['about']['image'] ?? null
            ];
            
            if ($request->hasFile('about_image')) {
                if (!empty($home_settings['about']['image'])) (new CustomController)->deleteFile($home_settings['about']['image']);
                $home_settings['about']['image'] = (new CustomController)->imageUpload($request->about_image);
            }
        }
        
        // Natural Relief Section
        if ($request->has('natural_relief_title')) {
            $reliefCards = [];
            foreach ($request->relief_card_title ?? [] as $index => $title) {
                $icon = $request->relief_card_icon_current[$index] ?? null;
                if ($request->hasFile("relief_card_icon.$index")) {
                    if ($icon) (new CustomController)->deleteFile($icon);
                    $icon = (new CustomController)->imageUpload($request->file("relief_card_icon.$index"));
                }
                $reliefCards[] = [
                    'title' => $title,
                    'btn_text' => $request->relief_card_btn_text[$index] ?? '',
                    'btn_url' => $request->relief_card_btn_url[$index] ?? '',
                    'icon' => $icon
                ];
            }
            
            $reliefImage = $home_settings['natural_relief']['image'] ?? null;
            if ($request->hasFile('natural_relief_image')) {
                if ($reliefImage) (new CustomController)->deleteFile($reliefImage);
                $reliefImage = (new CustomController)->imageUpload($request->natural_relief_image);
            }

            $home_settings['natural_relief'] = [
                'badge' => $request->natural_relief_badge,
                'title' => $request->natural_relief_title,
                'image' => $reliefImage,
                'btn1_text' => $request->natural_relief_btn1_text,
                'btn1_url' => $request->natural_relief_btn1_url,
                'btn2_text' => $request->natural_relief_btn2_text,
                'btn2_url' => $request->natural_relief_btn2_url,
                'cards' => $reliefCards
            ];
        }

        // ED Banner Section
        if ($request->has('ed_banner_title')) {
            $ed_banner = $home_settings['ed_banner'] ?? [];
            
            // Main Hero Image
            if ($request->hasFile('ed_banner_hero_image')) {
                if (!empty($ed_banner['hero_image'])) (new CustomController)->deleteFile($ed_banner['hero_image']);
                $ed_banner['hero_image'] = (new CustomController)->imageUpload($request->ed_banner_hero_image);
            }

            // Large Card Image
            if ($request->hasFile('ed_card_large_image')) {
                if (!empty($ed_banner['large_card']['image'])) (new CustomController)->deleteFile($ed_banner['large_card']['image']);
                $ed_banner['large_card']['image'] = (new CustomController)->imageUpload($request->ed_card_large_image);
            }

            // Right Card 1 Image
            if ($request->hasFile('ed_card_r1_image')) {
                if (!empty($ed_banner['right_card_1']['image'])) (new CustomController)->deleteFile($ed_banner['right_card_1']['image']);
                $ed_banner['right_card_1']['image'] = (new CustomController)->imageUpload($request->ed_card_r1_image);
            }

            // Right Card 2 Image
            if ($request->hasFile('ed_card_r2_image')) {
                if (!empty($ed_banner['right_card_2']['image'])) (new CustomController)->deleteFile($ed_banner['right_card_2']['image']);
                $ed_banner['right_card_2']['image'] = (new CustomController)->imageUpload($request->ed_card_r2_image);
            }

            $ed_banner['pill'] = $request->ed_banner_pill;
            $ed_banner['title'] = $request->ed_banner_title;
            $ed_banner['btn1_text'] = $request->ed_banner_btn1_text;
            $ed_banner['btn1_url'] = $request->ed_banner_btn1_url;
            $ed_banner['btn2_text'] = $request->ed_banner_btn2_text;
            $ed_banner['btn2_url'] = $request->ed_banner_btn2_url;

            $ed_banner['large_card']['title'] = $request->ed_card_large_title;
            $ed_banner['large_card']['btn_text'] = $request->ed_card_large_btn_text;
            $ed_banner['large_card']['btn_url'] = $request->ed_card_large_btn_url;

            $ed_banner['right_card_1']['title'] = $request->ed_card_r1_title;
            $ed_banner['right_card_1']['btn_text'] = $request->ed_card_r1_btn_text;
            $ed_banner['right_card_1']['btn_url'] = $request->ed_card_r1_btn_url;

            $ed_banner['right_card_2']['title'] = $request->ed_card_r2_title;
            $ed_banner['right_card_2']['btn_text'] = $request->ed_card_r2_btn_text;
            $ed_banner['right_card_2']['btn_url'] = $request->ed_card_r2_btn_url;

            $home_settings['ed_banner'] = $ed_banner;
        }

        // Testosterone Banner Section
        if ($request->has('testo_banner_title')) {
            $testo_banner = $home_settings['testosterone_banner'] ?? [];
            
            // Main Hero Image
            if ($request->hasFile('testo_banner_hero_image')) {
                if (!empty($testo_banner['hero_image'])) (new CustomController)->deleteFile($testo_banner['hero_image']);
                $testo_banner['hero_image'] = (new CustomController)->imageUpload($request->testo_banner_hero_image);
            }

            // Left Card Image
            if ($request->hasFile('testo_card_left_image')) {
                if (!empty($testo_banner['left_card']['image'])) (new CustomController)->deleteFile($testo_banner['left_card']['image']);
                $testo_banner['left_card']['image'] = (new CustomController)->imageUpload($request->testo_card_left_image);
            }

            // Right Card Image
            if ($request->hasFile('testo_card_right_image')) {
                if (!empty($testo_banner['right_card']['image'])) (new CustomController)->deleteFile($testo_banner['right_card']['image']);
                $testo_banner['right_card']['image'] = (new CustomController)->imageUpload($request->testo_card_right_image);
            }

            $testo_banner['pill'] = $request->testo_banner_pill;
            $testo_banner['title'] = $request->testo_banner_title;
            $testo_banner['btn1_text'] = $request->testo_banner_btn1_text;
            $testo_banner['btn1_url'] = $request->testo_banner_btn1_url;
            $testo_banner['btn2_text'] = $request->testo_banner_btn2_text;
            $testo_banner['btn2_url'] = $request->testo_banner_btn2_url;

            $testo_banner['left_card']['title'] = $request->testo_card_left_title;
            $testo_banner['left_card']['btn_text'] = $request->testo_card_left_btn_text;
            $testo_banner['left_card']['btn_url'] = $request->testo_card_left_btn_url;

            $testo_banner['right_card']['title'] = $request->testo_card_right_title;
            $testo_banner['right_card']['btn_text'] = $request->testo_card_right_btn_text;
            $testo_banner['right_card']['btn_url'] = $request->testo_card_right_btn_url;

            $home_settings['testosterone_banner'] = $testo_banner;
        }

        // Weight Loss Banner Section
        if ($request->has('wl_banner_title')) {
            $wl_banner = $home_settings['weight_loss_banner'] ?? [];
            
            // Main Hero Image
            if ($request->hasFile('wl_banner_hero_image')) {
                if (!empty($wl_banner['hero_image'])) (new CustomController)->deleteFile($wl_banner['hero_image']);
                $wl_banner['hero_image'] = (new CustomController)->imageUpload($request->wl_banner_hero_image);
            }

            // Left Card Image
            if ($request->hasFile('wl_card_left_image')) {
                if (!empty($wl_banner['left_card']['image'])) (new CustomController)->deleteFile($wl_banner['left_card']['image']);
                $wl_banner['left_card']['image'] = (new CustomController)->imageUpload($request->wl_card_left_image);
            }

            // Right Card Image
            if ($request->hasFile('wl_card_right_image')) {
                if (!empty($wl_banner['right_card']['image'])) (new CustomController)->deleteFile($wl_banner['right_card']['image']);
                $wl_banner['right_card']['image'] = (new CustomController)->imageUpload($request->wl_card_right_image);
            }

            $wl_banner['pill'] = $request->wl_banner_pill;
            $wl_banner['title'] = $request->wl_banner_title;
            $wl_banner['subtext'] = $request->wl_banner_subtext;
            $wl_banner['btn1_text'] = $request->wl_banner_btn1_text;
            $wl_banner['btn1_url'] = $request->wl_banner_btn1_url;
            $wl_banner['btn2_text'] = $request->wl_banner_btn2_text;
            $wl_banner['btn2_url'] = $request->wl_banner_btn2_url;

            $wl_banner['left_card']['title'] = $request->wl_card_left_title;
            $wl_banner['left_card']['btn_text'] = $request->wl_card_left_btn_text;
            $wl_banner['left_card']['btn_url'] = $request->wl_card_left_btn_url;

            $wl_banner['right_card']['title'] = $request->wl_card_right_title;
            $wl_banner['right_card']['btn_text'] = $request->wl_card_right_btn_text;
            $wl_banner['right_card']['btn_url'] = $request->wl_card_right_btn_url;

            $home_settings['weight_loss_banner'] = $wl_banner;
        }

        // Medical Advisory Board Section
        if ($request->has('advisors_heading')) {
            $advisors = $home_settings['medical_advisors'] ?? ['heading' => '', 'slots' => []];
            $advisors['heading'] = $request->advisors_heading;
            
            // Re-initialize array or update existing
            if (!isset($advisors['slots'])) $advisors['slots'] = [];
            
            for ($i = 0; $i < 6; $i++) {
                if ($request->has('advisor_name_'.$i) || $request->hasFile('advisor_image_'.$i)) {
                    $slot = $advisors['slots'][$i] ?? [];
                    
                    if ($request->has('advisor_name_'.$i)) {
                        $slot['name'] = $request->input('advisor_name_'.$i);
                    }
                    
                    if ($request->hasFile('advisor_image_'.$i)) {
                        if (!empty($slot['image'])) (new CustomController)->deleteFile($slot['image']);
                        $slot['image'] = (new CustomController)->imageUpload($request->file('advisor_image_'.$i));
                    }
                    
                    $advisors['slots'][$i] = $slot;
                }
            }
            $home_settings['medical_advisors'] = $advisors;
        }

        // Stats Section
        if ($request->has('stats_heading')) {
            $stats = [];
            $stats['heading'] = $request->stats_heading;
            
            $stats['left_card']['top_text'] = $request->stats_left_top;
            $stats['left_card']['number'] = $request->stats_left_number;
            $stats['left_card']['bottom_text'] = $request->stats_left_bottom;

            $stats['right_card']['top_text'] = $request->stats_right_top;
            $stats['right_card']['number'] = $request->stats_right_number;
            $stats['right_card']['bottom_text'] = $request->stats_right_bottom;
            
            $home_settings['stats_section'] = $stats;
        }

        // Comparison Section
        if ($request->has('comp_heading')) {
            $comp = $home_settings['comparison_section'] ?? [];
            $comp['pill'] = $request->comp_pill;
            $comp['heading'] = $request->comp_heading;
            $comp['subheading'] = $request->comp_subheading;
            
            if ($request->hasFile('comp_center_image')) {
                if (!empty($comp['center_image'])) (new CustomController)->deleteFile($comp['center_image']);
                $comp['center_image'] = (new CustomController)->imageUpload($request->file('comp_center_image'));
            }

            $comp['left_col_title'] = $request->comp_left_title;
            $comp['right_col_title'] = $request->comp_right_title;

            $comp['rows'] = [];
            for ($i = 0; $i < 5; $i++) {
                $comp['rows'][] = [
                    'left' => $request->input("comp_row_left_$i"),
                    'right' => $request->input("comp_row_right_$i")
                ];
            }

            $comp['btn_text'] = $request->comp_btn_text;
            $comp['btn_url'] = $request->comp_btn_url;
            $comp['btn_subtext'] = $request->comp_btn_subtext;

            $home_settings['comparison_section'] = $comp;
        }

        // FAQ Section
        if ($request->has('faq_heading')) {
            $faq = [];
            $faq['heading'] = $request->faq_heading;
            $faq['subheading'] = $request->faq_subheading;
            
            $faq['items'] = [];
            for ($i = 0; $i < 5; $i++) {
                if ($request->filled("faq_question_$i")) {
                    $faq['items'][] = [
                        'question' => $request->input("faq_question_$i"),
                        'answer' => $request->input("faq_answer_$i")
                    ];
                }
            }
            $home_settings['faq_section'] = $faq;
        }

        // Media Logos Section
        if ($request->has('media_heading')) {
            $media = [];
            $media['heading'] = $request->media_heading;
            $media['items'] = [];
            for ($i = 0; $i < 8; $i++) {
                if ($request->filled("media_item_$i")) {
                    $media['items'][] = $request->input("media_item_$i");
                }
            }
            $home_settings['media_section'] = $media;
        }

        // Final CTA Section
        if ($request->has('cta_heading')) {
            $home_settings['cta_section'] = [
                'heading' => $request->cta_heading,
                'btn_text' => $request->cta_btn_text,
                'btn_url' => $request->cta_btn_url,
                'subtext' => $request->cta_subtext
            ];
        }

        // Privacy Section
        if ($request->has('priv_label')) {
            $priv = [];
            $priv['label'] = $request->priv_label;
            $priv['heading_1'] = $request->priv_heading_1;
            $priv['heading_2'] = $request->priv_heading_2;
            $priv['subtext'] = $request->priv_subtext;
            
            $priv['features'] = [];
            for ($i = 0; $i < 4; $i++) {
                if ($request->filled("priv_feature_$i")) {
                    $priv['features'][] = $request->input("priv_feature_$i");
                }
            }
            
            $priv['pills'] = [];
            for ($i = 0; $i < 3; $i++) {
                if ($request->filled("priv_pill_$i")) {
                    $priv['pills'][] = $request->input("priv_pill_$i");
                }
            }
            $home_settings['privacy_section'] = $priv;
        }

        $data['website_home_settings'] = json_encode($home_settings);

        // Handle Footer Settings
        if ($request->has('footer_copy')) {
            $columns = [];
            foreach ($request->footer_col_title ?? [] as $index => $title) {
                $links = [];
                // Expecting links as a newline separated string or another repeater. 
                // Let's use a simple newline for now or a sub-JSON if needed.
                $col_links_raw = $request->footer_col_links[$index] ?? '';
                $lines = explode("\n", str_replace("\r", "", $col_links_raw));
                foreach($lines as $line) {
                    if (strpos($line, '|') !== false) {
                        list($l, $u) = explode('|', $line);
                        $links[] = ['label' => trim($l), 'url' => trim($u)];
                    }
                }
                $columns[] = [
                    'title' => $title,
                    'links' => $links
                ];
            }
            $footer_settings = [
                'copy' => $request->footer_copy,
                'columns' => $columns,
                'facebook' => $request->facebook_url,
                'twitter' => $request->twitter_url,
                'instagram' => $request->instagram_url,
                'linkedin' => $request->linkdin_url
            ];
            $data['website_footer_settings'] = json_encode($footer_settings);
        }

        // abort(403, json_encode($data)); // DEBUG
        $setting->landing_popup_switch = $request->has('landing_popup_switch') ? 1 : 0;
        $setting->website_header_search = $request->has('website_header_search') ? 1 : 0;
        $setting->website_header_user = $request->has('website_header_user') ? 1 : 0;
        $setting->website_header_hamburger = $request->has('website_header_hamburger') ? 1 : 0;

        $setting->update($data);

        return redirect()->back()->withStatus(__('Website Setting updated successfully.!'));
    }

    public function update_notification(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'patient_app_id' => 'bail|required_if:patient_notification,1',
            'patient_auth_key' => 'bail|required_if:patient_notification,1',
            'patient_api_key' => 'bail|required_if:patient_notification,1',
            'doctor_app_id' => 'bail|required_if:doctor_notification,1',
            'doctor_auth_key' => 'bail|required_if:doctor_notification,1',
            'doctor_api_key' => 'bail|required_if:doctor_notification,1',
        ]);
        $data = $request->all();
        $data['patient_mail'] = $request->has('patient_mail') ? 1 : 0;
        $data['doctor_mail'] = $request->has('doctor_mail') ? 1 : 0;
        $data['doctor_notification'] = $request->has('doctor_notification') ? 1 : 0;
        $data['patient_notification'] = $request->has('patient_notification') ? 1 : 0;
        Setting::first()->update($data);

        return redirect()->back()->withStatus(__('Notification setting updated successfully..!!'));
    }

    public function update_licence_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'license_code' => 'required',
            'client_name' => 'required',
        ]);
        $api = new LicenseBoxExternalAPI;
        $result = $api->activate_license($request->license_code, $request->client_name);
        if ($result['status'] == true) {
            $id = Setting::find(1);
            $data = $request->all();
            $data['license_verify'] = 1;
            $id->update($data);

            return redirect('/login');
        } else {
            return redirect()->back()->with('error_msg', $result['message']);
        }

        return redirect('admin/setting');
    }

    public function update_static_page(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Setting::first()->update($request->all());

        return redirect()->back()->withStatus(__('Setting updated successfully..!!'));
    }

    public function update_video_call_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Setting::first()->update($request->all());

        return redirect()->back()->withStatus(__('Setting updated successfully..!!'));
    }

    public function update_zoom_setting(Request $request)
    {
        abort_if(Gate::denies('superadmin_setting'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'zoom_client_id' => 'required_if:zoom_switch,1',
            'zoom_client_secret' => 'required_if:zoom_switch,1',
            'zoom_redirect_url' => 'required_if:zoom_switch,1',
        ], [
            'zoom_client_id.required_if' => 'The Zoom Client ID field is required when Zoom is enabled.',
            'zoom_client_secret.required_if' => 'The Zoom Client Secret field is required when Zoom is enabled.',
            'zoom_redirect_url.required_if' => 'The Zoom Redirect URL field is required when Zoom is enabled.',
        ]);
        $data = $request->all();
        $data['zoom_switch'] = $request->has('zoom_switch') ? 1 : 0;
        Setting::first()->update($data);

        return redirect()->back()->withStatus(__('Zoom settings updated successfully..!!'));
    }
}
