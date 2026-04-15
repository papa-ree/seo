{{--
Bale SEO Meta Component

Usage: <x-seo::seo-meta :model="$post" />

The model should use Bale\Seo\Traits\HasSeoMeta trait.
Fallback values will be used if seo_meta is not set.
--}}

@props(['model' => null, 'defaults' => []])

@php
    // Get defaults from config
    $configDefaults = config('seo.defaults', []);

    // Merge with passed defaults
    $defaultTitle = $defaults['title'] ?? $configDefaults['title'] ?? config('app.name', 'Website');
    $defaultDescription = $defaults['description'] ?? $configDefaults['description'] ?? '';
    $defaultImage = $defaults['image'] ?? $configDefaults['image'] ?? asset('img/og-image.jpg');
    $defaultKeywords = $defaults['keywords'] ?? $configDefaults['keywords'] ?? '';

    // Get SEO values from model or use defaults
    if ($model && method_exists($model, 'getSeoTitle')) {
        $title = $model->getSeoTitle() ?: $defaultTitle;
        $description = $model->getSeoDescription() ?: $defaultDescription;
        $ogTitle = $model->getOgTitle() ?: $title;
        $ogDescription = $model->getOgDescription() ?: $description;
        $ogImage = $model->getOgImage() ?: $defaultImage;
        $keywords = $model->getSeoKeywords() ?: $defaultKeywords;
        $canonical = $model->getCanonicalUrl() ?: url()->current();
        $robots = $model->getSeoRobots();
        $structuredData = $model->getStructuredData();
        $ogType = method_exists($model, 'getOgType') ? $model->getOgType() : 'website';
        $twitterCard = method_exists($model, 'getTwitterCardType') ? $model->getTwitterCardType() : 'summary_large_image';
    } else {
        $title = $defaultTitle;
        $description = $defaultDescription;
        $ogTitle = $title;
        $ogDescription = $description;
        $ogImage = $defaultImage;
        $keywords = $defaultKeywords;
        $canonical = url()->current();
        $robots = 'index, follow';
        $structuredData = null;
        $ogType = 'website';
        $twitterCard = 'summary_large_image';
    }

    $siteName = config('seo.site_name', config('app.name'));
    $locale = config('app.locale', 'id_ID') === 'id' ? 'id_ID' : 'en_US';

    // Ensure OG Image is absolute
    if ($ogImage && !str_starts_with($ogImage, 'http') && !str_starts_with($ogImage, '//')) {
        $ogImage = url($ogImage);
    }
@endphp

{{-- Basic Meta Tags --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $locale }}">
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif

{{-- Structured Data (JSON-LD) --}}
@if($structuredData)
    <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>
@elseif($model)
    <script type="application/ld+json">
            {
                "@@context": "https://schema.org",
                "@@type": "Article",
                "headline": "{!! addslashes($title) !!}",
                "description": "{!! addslashes($description) !!}",
                @if($ogImage)
                    "image": "{{ $ogImage }}",
                @endif
                "url": "{{ url()->current() }}",
                "dateModified": "{{ isset($model->updated_at) ? $model->updated_at : now()->toIso8601String() }}"
            }
            </script>
@endif