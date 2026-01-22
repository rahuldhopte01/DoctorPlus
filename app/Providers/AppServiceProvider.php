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
    }
}
