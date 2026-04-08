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
                $sidebarCatsJson = json_decode($setting->website_sidebar_categories, true) ?: [];
                
                if (count($sidebarCatsJson) > 0) {
                    $catIds = array_column($sidebarCatsJson, 'id');
                    $isNewMap = array_column($sidebarCatsJson, 'is_new', 'id');
                    
                    // Fetch categories and map the 'is_new' status
                    $selectedCategories = \App\Models\Category::with('treatment')
                        ->whereStatus(1)
                        ->whereIn('id', $catIds)
                        ->get()
                        ->each(function($cat) use ($isNewMap) {
                            $cat->is_sidebar_new = $isNewMap[$cat->id] ?? 0;
                        });

                    // Group by Treatment to maintain hierarchy
                    $grouped = $selectedCategories->groupBy('treatment_id');
                    $sidebar_treatments = collect();
                    
                    foreach ($grouped as $treatmentId => $cats) {
                        // We need the Treatment model as a container
                        $treatment = \App\Models\Treatments::find($treatmentId);
                        if ($treatment && $treatment->status == 1) {
                            $treatment->setRelation('category', $cats);
                            $sidebar_treatments->push($treatment);
                        }
                    }
                } else {
                    $sidebar_treatments = collect();
                }
                
                $categories = \App\Models\Category::with('treatment')->whereStatus(1)->orderBy('name', 'ASC')->get();
                
                $footerSettings = json_decode($setting->website_footer_settings, true) ?: [];
                $footer_cols = $footerSettings['columns'] ?? [];

                $view->with('sidebar_treatments', $sidebar_treatments);
                $view->with('categories', $categories);
                $view->with('sidebar_footer_cols', $footer_cols);
            } catch (\Exception $e) {
                $view->with('sidebar_treatments', collect());
                $view->with('categories', collect());
                $view->with('sidebar_footer_cols', []);
            }
        });
    }
}
