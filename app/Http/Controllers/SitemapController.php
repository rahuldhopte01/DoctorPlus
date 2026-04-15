<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Pharmacy;
use App\Models\Setting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate and return the sitemap.xml
     */
    public function index()
    {
        $setting  = Setting::first();
        $base     = url('/');

        // Static pages
        $staticUrls = [
            ['loc' => $base, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('display_doctors'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => url('display_pharmacy'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('our_blogs'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('our_offers'), 'changefreq' => 'weekly', 'priority' => '0.6'],
        ];

        // Categories
        $categories = Category::whereStatus(1)->get(['id', 'updated_at']);

        // Doctors
        $doctors = Doctor::whereStatus(1)->where('is_filled', 1)->get(['id', 'updated_at']);

        // Pharmacies
        $pharmacies = Pharmacy::where('status', 'approved')->get(['id', 'updated_at']);

        // Blogs
        $blogs = Blog::get(['id', 'updated_at']);

        $xml = response()->view('sitemap', compact('staticUrls', 'categories', 'doctors', 'pharmacies', 'blogs'))
            ->header('Content-Type', 'application/xml');

        return $xml;
    }

    /**
     * Dynamic robots.txt based on google_indexing setting
     */
    public function robots()
    {
        $setting = Setting::first();

        if ($setting && $setting->google_indexing == 0) {
            // Tell all crawlers not to index anything
            $content = "User-agent: *\nDisallow: /\n";
        } else {
            // Allow indexing, disallow admin paths
            $content = "User-agent: *\nDisallow: /home\nDisallow: /setting\nDisallow: /login\n\nSitemap: " . url('sitemap.xml') . "\n";
        }

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
