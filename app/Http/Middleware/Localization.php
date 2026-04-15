<?php

namespace App\Http\Middleware;

use App;
use App\Models\Role;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('DB_DATABASE')) {

            if (session()->has('locale') && session()->has('direction')) {
                // Session locale takes priority (set when user manually switches language)
                $language = session()->get('locale');
            } elseif (auth()->check() && auth()->user()->language) {
                // Fall back to user's saved language preference
                $language = auth()->user()->language;
                // Log::error($language);
            } else {
                $language = Setting::first()->language;
                // Log::error($language);
            }
            if ($language) {
                // dd($language);
                // Log::error($language);
                $languageRecord = \App\Models\Language::where('name', $language)->first();
                $direction = $languageRecord ? $languageRecord->direction : 'ltr';
                App::setLocale($language);
                session()->put('locale', $language);
                session()->put('direction', $direction);
            }
        }

        return $next($request);
    }
}
