<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Dynamically set mail configuration from database
        try {
            $setting = Setting::first();
            
            if ($setting) {
                // Set default mailer
                if ($setting->mail_mailer) {
                    Config::set('mail.default', $setting->mail_mailer);
                }
                
                // Configure SMTP mailer
                if ($setting->mail_host) {
                    Config::set('mail.mailers.smtp.host', $setting->mail_host);
                }
                if ($setting->mail_port) {
                    Config::set('mail.mailers.smtp.port', $setting->mail_port);
                }
                if ($setting->mail_username) {
                    Config::set('mail.mailers.smtp.username', $setting->mail_username);
                }
                if ($setting->mail_password) {
                    Config::set('mail.mailers.smtp.password', $setting->mail_password);
                }
                if ($setting->mail_encryption) {
                    Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption);
                }
                
                // Set from address and name
                if ($setting->mail_from_address) {
                    Config::set('mail.from.address', $setting->mail_from_address);
                }
                if ($setting->mail_from_name) {
                    Config::set('mail.from.name', $setting->mail_from_name);
                }
            }
        } catch (\Exception $e) {
            // If database is not available or settings table doesn't exist, use env() fallback
            // This prevents errors during migrations or when database is not ready
        }

        view()->composer('layout.partials.navbar_website', function ($view) {
            try {
                $setting = \App\Models\Setting::first();
                
                // 1. TOP-KATEGORIEN
                $sidebarCatsJson = json_decode($setting->website_sidebar_categories, true) ?: [];
                $sidebar_top_items = collect();
                if (count($sidebarCatsJson) > 0) {
                    foreach ($sidebarCatsJson as $item) {
                        $cat = \App\Models\Category::with('treatment')->whereStatus(1)->find($item['id']);
                        if ($cat) {
                            $cat->sidebar_custom_title = !empty($item['custom_title']) ? $item['custom_title'] : ($cat->treatment->name ?? $cat->name);
                            $cat->is_sidebar_new = $item['is_new'] ?? 0;
                            $sidebar_top_items->push($cat);
                        }
                    }
                }

                // 2. ENTDECKEN — treatment-based with optional sub-items
                $entdeckenJson = json_decode($setting->website_sidebar_entdecken, true) ?: [];
                $sidebar_entdecken_items = collect();
                foreach ($entdeckenJson as $item) {
                    $treatId = $item['treatment_id'] ?? null;
                    if (!$treatId) continue;
                    $treatment = \App\Models\Treatments::find($treatId);
                    if (!$treatment) continue;
                    $mode  = $item['mode'] ?? 'link';
                    $label = !empty($item['custom_label']) ? $item['custom_label'] : $treatment->name;
                    if ($mode === 'dropdown') {
                        // Build sub-items from saved list
                        $subItems = [];
                        foreach ($item['sub_items'] ?? [] as $sub) {
                            $subItems[] = [
                                'label'       => $sub['label'] ?? '',
                                'url'         => $sub['url']   ?? '#',
                                'category_id' => $sub['category_id'] ?? null,
                            ];
                        }
                        $sidebar_entdecken_items->push((object)[
                            'mode'      => 'dropdown',
                            'label'     => $label,
                            'sub_items' => $subItems,
                        ]);
                    } else {
                        $sidebar_entdecken_items->push((object)[
                            'mode'  => 'link',
                            'label' => $label,
                            'url'   => $item['url'] ?? '#',
                        ]);
                    }
                }

                // 3. LERNEN SIE DR.FUXX KENNEN
                $sidebar_lernen_items = json_decode($setting->website_sidebar_lernen, true) ?: [];
                
                $categories = \App\Models\Category::with('treatment')->whereStatus(1)->orderBy('name', 'ASC')->get();
                $footerSettings = json_decode($setting->website_footer_settings, true) ?: [];
                $footer_cols = $footerSettings['columns'] ?? [];

                $view->with('sidebar_top_items', $sidebar_top_items);
                $view->with('sidebar_entdecken_items', $sidebar_entdecken_items);
                $view->with('sidebar_lernen_items', $sidebar_lernen_items);
                $view->with('categories', $categories);
                $view->with('sidebar_footer_cols', $footer_cols);
                $view->with('setting', $setting);
                
                // Deprecated (keeping for compatibility during transition if needed)
                $view->with('sidebar_treatments', collect()); 
            } catch (\Exception $e) {
                \Log::error("Sidebar view composer error: " . $e->getMessage());
                $view->with('sidebar_top_items', collect());
                $view->with('sidebar_entdecken_items', collect());
                $view->with('sidebar_lernen_items', []);
                $view->with('categories', collect());
                $view->with('sidebar_footer_cols', []);
                $view->with('sidebar_treatments', collect());
            }
        });
    }
}
