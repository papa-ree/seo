<?php

return [
    /**
     * Site name for SEO purposes.
     * Fallback to app.name if not set.
     */
    'site_name' => env('SEO_SITE_NAME', config('app.name', 'Bale')),

    /**
     * Whether to load SEO routes (sitemap, robots.txt).
     * Typically only true for landing pages / front-end.
     */
    'use_routes' => env('SEO_USE_ROUTES', false),

    /**
     * Default meta tags.
     */
    'defaults' => [
        'title' => env('SEO_DEFAULT_TITLE', config('app.name', 'Bale')),
        'description' => env('SEO_DEFAULT_DESCRIPTION', ''),
        'image' => env('SEO_DEFAULT_IMAGE', '/img/og-image.jpg'),
        'keywords' => env('SEO_DEFAULT_KEYWORDS', ''),
    ],

    /**
     * Sitemap configuration. Please setup model here
     */
    'sitemap' => [
        'models' => [
            'post' => \Bale\Emperan\Models\Post::class,
            'page' => \Bale\Emperan\Models\Page::class,
        ],
    ],
];
