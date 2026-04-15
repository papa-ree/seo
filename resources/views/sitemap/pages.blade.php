<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $page)
    <url>
        <loc>{{ url('/page/' . $page->slug) }}</loc>
        <lastmod>{{ \Carbon\Carbon::parse($page->getRawOriginal('updated_at'))->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>
