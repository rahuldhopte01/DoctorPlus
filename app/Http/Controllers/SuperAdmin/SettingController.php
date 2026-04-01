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
        $request->validate([
            'popup_target_url' => 'bail|required',
        ]);
        $setting = Setting::first();
        $data = $request->all();

        // Banner image
        if ($request->hasFile('banner_image')) {
            (new CustomController)->deleteFile($setting->banner_image);
            $data['banner_image'] = (new CustomController)->imageUpload($request->banner_image);
        }
        if ($request->hasFile('landing_popup_image')) {
            (new CustomController)->deleteFile($setting->landing_popup_image);
            $data['landing_popup_image'] = (new CustomController)->imageUpload($request->landing_popup_image);
        }
        $setting->landing_popup_switch = $request->has('landing_popup_switch') ? 1 : 0;

        // Header checkboxes
        $data['website_header_search']    = $request->has('website_header_search') ? 1 : 0;
        $data['website_header_user']      = $request->has('website_header_user') ? 1 : 0;
        $data['website_header_hamburger'] = $request->has('website_header_hamburger') ? 1 : 0;

        // Header logo
        if ($request->hasFile('website_header_logo')) {
            $data['website_header_logo'] = (new CustomController)->imageUpload($request->file('website_header_logo'));
        }

        // Promo bar JSON
        $data['website_header_promo_bar'] = json_encode([
            'status'      => $request->has('promo_status') ? 1 : 0,
            'text_italic' => $request->promo_text_italic,
            'text_bold'   => $request->promo_text_bold,
            'end_date'    => $request->promo_end_date,
        ]);

        // Top marquee JSON (with file uploads)
        $marquees = collect($request->marquee_text ?? [])->map(function ($text, $i) use ($request) {
            $icon = $request->marquee_icon_current[$i] ?? null;
            if ($request->hasFile('marquee_icon') && isset($request->file('marquee_icon')[$i])) {
                $icon = (new CustomController)->imageUpload($request->file('marquee_icon')[$i]);
            }
            return ['text' => $text, 'icon' => $icon];
        })->toArray();
        $data['website_header_top_marquee'] = json_encode($marquees);

        // Sidebar menu JSON
        $data['website_header_sidebar_menu'] = json_encode(
            collect($request->menu_label ?? [])->map(fn($l, $i) => [
                'label' => $l,
                'url'   => $request->menu_url[$i] ?? '#',
            ])->toArray()
        );

        // ---- Home Page Settings ----
        $existingHome = json_decode($setting->website_home_settings, true) ?: [];

        // Hero image uploads
        $heroImage   = $existingHome['hero']['image'] ?? null;
        $heroBgImage = $existingHome['hero']['bg_image'] ?? null;
        if ($request->hasFile('hero_image')) {
            $heroImage = (new CustomController)->imageUpload($request->file('hero_image'));
        }
        if ($request->hasFile('hero_bg_image')) {
            $heroBgImage = (new CustomController)->imageUpload($request->file('hero_bg_image'));
        }

        // Trust items
        $trustItems = collect($request->hero_trust_icon_class ?? [])->map(fn($ic, $i) => [
            'icon_class' => $ic,
            'text'       => $request->hero_trust_text[$i] ?? '',
        ])->toArray();

        // Quick link cards (indexed file uploads)
        $quickLinks = collect($request->hero_quick_link_title ?? [])->map(function ($title, $i) use ($request, $existingHome) {
            $image = $request->hero_quick_link_image_current[$i] ?? ($existingHome['hero']['quick_links'][$i]['image'] ?? null);
            if ($request->hasFile('hero_quick_link_image') && isset($request->file('hero_quick_link_image')[$i])) {
                $image = (new CustomController)->imageUpload($request->file('hero_quick_link_image')[$i]);
            }
            return [
                'image'      => $image,
                'title'      => $title,
                'subtitle'   => $request->hero_quick_link_subtitle[$i] ?? '',
                'badge'      => $request->hero_quick_link_badge[$i] ?? '',
                'url'        => $request->hero_quick_link_url[$i] ?? '#',
                'icon_class' => $request->hero_quick_link_icon_class[$i] ?? '',
            ];
        })->toArray();

        // How it Works steps
        $steps = collect($request->step_title ?? [])->map(function ($title, $i) use ($request, $existingHome) {
            $icon = $request->step_icon_current[$i] ?? ($existingHome['how_it_works']['steps'][$i]['icon'] ?? null);
            if ($request->hasFile('step_icon') && isset($request->file('step_icon')[$i])) {
                $icon = (new CustomController)->imageUpload($request->file('step_icon')[$i]);
            }
            return [
                'icon'  => $icon,
                'title' => $title,
                'text'  => $request->step_text[$i] ?? '',
            ];
        })->toArray();

        // Natural Relief image
        $reliefImage = $existingHome['natural_relief']['image'] ?? null;
        if ($request->hasFile('natural_relief_image')) {
            $reliefImage = (new CustomController)->imageUpload($request->file('natural_relief_image'));
        }

        // About image
        $aboutImage = $existingHome['about']['image'] ?? null;
        if ($request->hasFile('about_image')) {
            $aboutImage = (new CustomController)->imageUpload($request->file('about_image'));
        }

        // ED Banner hero image + cards
        $edHeroImage  = $existingHome['ed_banner']['hero_image'] ?? null;
        $edLargeImage = $existingHome['ed_banner']['large_card']['image'] ?? null;
        $edR1Image    = $existingHome['ed_banner']['right_card_1']['image'] ?? null;
        $edR2Image    = $existingHome['ed_banner']['right_card_2']['image'] ?? null;
        if ($request->hasFile('ed_banner_hero_image'))  $edHeroImage  = (new CustomController)->imageUpload($request->file('ed_banner_hero_image'));
        if ($request->hasFile('ed_card_large_image'))   $edLargeImage = (new CustomController)->imageUpload($request->file('ed_card_large_image'));
        if ($request->hasFile('ed_card_r1_image'))      $edR1Image    = (new CustomController)->imageUpload($request->file('ed_card_r1_image'));
        if ($request->hasFile('ed_card_r2_image'))      $edR2Image    = (new CustomController)->imageUpload($request->file('ed_card_r2_image'));

        // Testosterone bg image
        $testoBg = $existingHome['testosterone']['bg_image'] ?? null;
        if ($request->hasFile('testo_bg_image')) {
            $testoBg = (new CustomController)->imageUpload($request->file('testo_bg_image'));
        } elseif ($request->filled('testo_bg_image_current')) {
            $testoBg = $request->testo_bg_image_current;
        }

        // Advisory doctor images
        $doctors = collect($request->doctor_name ?? [])->map(function ($name, $i) use ($request, $existingHome) {
            $image = $request->doctor_image_current[$i] ?? ($existingHome['advisory']['doctors'][$i]['image'] ?? null);
            if ($request->hasFile('doctor_image') && isset($request->file('doctor_image')[$i])) {
                $image = (new CustomController)->imageUpload($request->file('doctor_image')[$i]);
            }
            return [
                'name'  => $name,
                'role'  => $request->doctor_role[$i] ?? '',
                'image' => $image,
            ];
        })->toArray();

        // Comparison bg image
        $compareBg = $existingHome['comparison']['bg_image'] ?? null;
        if ($request->hasFile('compare_bg_image')) {
            $compareBg = (new CustomController)->imageUpload($request->file('compare_bg_image'));
        } elseif ($request->filled('compare_bg_image_current')) {
            $compareBg = $request->compare_bg_image_current;
        }

        // Privacy image
        $privacyImage = $existingHome['privacy_section']['image'] ?? null;
        if ($request->hasFile('privacy_image')) {
            $privacyImage = (new CustomController)->imageUpload($request->file('privacy_image'));
        } elseif ($request->filled('privacy_image_current')) {
            $privacyImage = $request->privacy_image_current;
        }

        // Newsletter bg image
        $newsletterBg = $existingHome['newsletter']['bg_image'] ?? null;
        if ($request->hasFile('newsletter_bg_image')) {
            $newsletterBg = (new CustomController)->imageUpload($request->file('newsletter_bg_image'));
        } elseif ($request->filled('newsletter_bg_image_current')) {
            $newsletterBg = $request->newsletter_bg_image_current;
        }

        $data['website_home_settings'] = json_encode([
            'hero' => [
                'image'           => $heroImage,
                'bg_image'        => $heroBgImage,
                'bg_color'        => $request->hero_bg_color ?? '#f3ecff',
                'badge'           => $request->hero_badge,
                'title'           => $request->hero_title,
                'typing_keywords' => $request->hero_typing_keywords,
                'description'     => $request->hero_description,
                'btn_text'        => $request->hero_btn_text,
                'btn_url'         => $request->hero_btn_url ?? '#',
                'rating_stars'    => $request->hero_rating_stars ?? '5',
                'rating_score'    => $request->hero_rating_score ?? '4,79',
                'rating_text'     => $request->hero_rating_text,
                'live_viewers'    => $request->hero_live_viewers,
                'trust_items'     => $trustItems,
                'quick_links'     => $quickLinks,
            ],
            'how_it_works' => [
                'title'    => $request->how_it_works_title,
                'subtitle' => $request->how_it_works_subtitle,
                'badge'    => $request->how_it_works_badge,
                'steps'    => $steps,
            ],
            'natural_relief' => [
                'badge'      => $request->natural_relief_badge,
                'title'      => $request->natural_relief_title,
                'image'      => $reliefImage,
                'btn1_text'  => $request->natural_relief_btn1_text,
                'btn1_url'   => $request->natural_relief_btn1_url ?? '#',
                'btn2_text'  => $request->natural_relief_btn2_text,
                'btn2_url'   => $request->natural_relief_btn2_url ?? '#',
                'categories' => collect($request->relief_cat ?? [])->map(fn($c) => ['name' => $c])->toArray(),
            ],
            'about' => [
                'badge'       => $request->about_badge,
                'title'       => $request->about_title,
                'description' => $request->about_description,
                'image'       => $aboutImage,
                'features'    => $request->about_features ?? [],
            ],
            'ed_banner' => [
                'pill'         => $request->ed_banner_pill,
                'title'        => $request->ed_banner_title,
                'hero_image'   => $edHeroImage,
                'btn1_text'    => $request->ed_banner_btn1_text,
                'btn1_url'     => $request->ed_banner_btn1_url ?? '#',
                'btn2_text'    => $request->ed_banner_btn2_text,
                'btn2_url'     => $request->ed_banner_btn2_url ?? '#',
                'large_card'   => [
                    'image'    => $edLargeImage,
                    'title'    => $request->ed_card_large_title,
                    'btn_text' => $request->ed_card_large_btn_text,
                    'btn_url'  => $request->ed_card_large_btn_url ?? '#',
                ],
                'right_card_1' => [
                    'image'    => $edR1Image,
                    'title'    => $request->ed_card_r1_title,
                    'btn_text' => $request->ed_card_r1_btn_text,
                    'btn_url'  => $request->ed_card_r1_btn_url ?? '#',
                ],
                'right_card_2' => [
                    'image'    => $edR2Image,
                    'title'    => $request->ed_card_r2_title,
                    'btn_text' => $request->ed_card_r2_btn_text,
                    'btn_url'  => $request->ed_card_r2_btn_url ?? '#',
                ],
            ],
            'sub_categories' => collect($request->sub_cat_text ?? [])->map(fn($t) => ['text' => $t])->toArray(),
            'trust_banner' => [
                'text' => $request->trust_banner_text,
            ],
            'testosterone' => [
                'pill'      => $request->testo_pill,
                'title'     => $request->testo_title,
                'btn1_text' => $request->testo_btn1_text,
                'btn1_url'  => $request->testo_btn1_url ?? '#',
                'btn2_text' => $request->testo_btn2_text,
                'btn2_url'  => $request->testo_btn2_url ?? '#',
                'bg_image'  => $testoBg,
            ],
            'advisory' => [
                'title'   => $request->advisory_title,
                'doctors' => $doctors,
            ],
            'stats' => [
                'subtitle' => $request->stats_subtitle,
                'items'    => collect($request->stat_label ?? [])->map(fn($l, $i) => [
                    'label'  => $l,
                    'number' => $request->stat_number[$i] ?? '',
                    'title'  => $request->stat_title[$i] ?? '',
                ])->toArray(),
            ],
            'comparison' => [
                'title'    => $request->compare_title,
                'bg_image' => $compareBg,
                'rows'     => collect($request->compare_left ?? [])->map(fn($l, $i) => [
                    'left'  => $l,
                    'right' => $request->compare_right[$i] ?? '',
                ])->toArray(),
            ],
            'faq' => [
                'title'    => $request->faq_title,
                'subtitle' => $request->faq_subtitle,
                'items'    => collect($request->faq_question ?? [])->map(fn($q, $i) => [
                    'question' => $q,
                    'answer'   => $request->faq_answer[$i] ?? '',
                ])->toArray(),
            ],
            'press' => [
                'label' => $request->press_label,
                'logos' => collect($request->press_name ?? [])->map(fn($n) => ['name' => $n])->toArray(),
            ],
            'mid_cta' => [
                'heading'  => $request->mid_cta_heading,
                'subtext'  => $request->mid_cta_subtext,
                'btn_text' => $request->mid_cta_btn_text,
                'btn_url'  => $request->mid_cta_btn_url ?? '#',
                'note'     => $request->mid_cta_note,
            ],
            'privacy_section' => [
                'heading'     => $request->privacy_heading,
                'span'        => $request->privacy_span,
                'description' => $request->privacy_description,
                'image'       => $privacyImage,
            ],
            'newsletter' => [
                'heading'     => $request->newsletter_heading,
                'description' => $request->newsletter_description,
                'legal_text'  => $request->newsletter_legal,
                'bg_image'    => $newsletterBg,
            ],
        ]);

        // Footer settings JSON
        $footerColumns = collect($request->footer_col_title ?? [])->map(function ($title, $i) use ($request) {
            $rawLinks = $request->footer_col_links[$i] ?? '';
            $links = collect(explode("\n", $rawLinks))->filter()->map(function ($line) {
                $parts = explode('|', trim($line), 2);
                return ['label' => trim($parts[0] ?? ''), 'url' => trim($parts[1] ?? '#')];
            })->toArray();
            return ['title' => $title, 'links' => $links];
        })->toArray();
        $data['website_footer_settings'] = json_encode([
            'copy'    => $request->footer_copy,
            'columns' => $footerColumns,
        ]);

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
