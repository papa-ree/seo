<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($sitemaps as $sitemap)
    <sitemap>
        <loc>{{ $sitemap['loc'] }}</loc>
        @if($sitemap['lastmod'])
        <lastmod>{{ \Carbon\Carbon::parse($sitemap['lastmod'])->toW3cString() }}</lastmod>
        @endif
    </sitemap>
@endforeach
</sitemapindex>
