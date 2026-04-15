<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Static pages --}}
    @foreach($staticUrls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
    </url>
    @endforeach

    {{-- Categories --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('category/' . $category->id) }}</loc>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Doctors --}}
    @foreach($doctors as $doctor)
    <url>
        <loc>{{ url('doctor_detail/' . $doctor->id) }}</loc>
        <lastmod>{{ $doctor->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Pharmacies --}}
    @foreach($pharmacies as $pharmacy)
    <url>
        <loc>{{ url('pharmacy_detail/' . $pharmacy->id) }}</loc>
        <lastmod>{{ $pharmacy->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- Blogs --}}
    @foreach($blogs as $blog)
    <url>
        <loc>{{ url('our_blog_single/' . $blog->id) }}</loc>
        <lastmod>{{ $blog->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

</urlset>
