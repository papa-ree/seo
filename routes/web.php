<?php

use Illuminate\Support\Facades\Route;
use Bale\Seo\Controllers\RobotsController;
use Bale\Seo\Controllers\SitemapController;

if (config('seo.use_routes', false)) {
    // SEO routes - Sitemap
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])
        ->name('sitemap.index');
    Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])
        ->name('sitemap.posts');
    Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])
        ->name('sitemap.pages');

    // SEO routes - Robots
    Route::get('/robots.txt', [RobotsController::class, 'index'])
        ->name('robots.txt');
}
