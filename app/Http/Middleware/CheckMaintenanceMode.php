<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Paths that are always accessible even during maintenance.
     * These allow the admin to log in and turn maintenance off.
     */
    protected $except = [
        'login',
        'logout',
        'logout-get',
        'home',
        'setting',
        'update_seo_setting',
        'maintenance',
        'admin/*',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Skip for excluded paths
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Only super admin bypasses maintenance — all other users (doctors, patients, etc.) still see the maintenance page
        if (Auth::check() && Auth::user()->hasRole('super admin')) {
            return $next($request);
        }

        $setting = Setting::first();

        if ($setting && $setting->maintenance_mode == 1) {
            return response()->view('maintenance', ['setting' => $setting], 503);
        }

        return $next($request);
    }
}
