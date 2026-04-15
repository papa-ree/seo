<?php

namespace Bale\Seo\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class RobotsController extends Controller
{
    /**
     * Generate dynamic robots.txt
     */
    public function index(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = <<<ROBOTS
# Robots.txt for {$this->getSiteName()}
# Generated dynamically by Bale

User-agent: *
Allow: /

# Disallow admin and internal paths
Disallow: /admin
Disallow: /admin/*
Disallow: /livewire/*
Disallow: /api/*
Disallow: /_debugbar/*
Disallow: /telescope/*

# Disallow authentication pages
Disallow: /login
Disallow: /register
Disallow: /password/*

# Allow important resources
Allow: /media/*
Allow: /storage/*

# Sitemap
Sitemap: {$sitemapUrl}

# Crawl-delay (optional, for polite crawling)
Crawl-delay: 1

# Google specific
User-agent: Googlebot
Allow: /

# Bing specific  
User-agent: Bingbot
Allow: /

# Block bad bots (optional)
User-agent: AhrefsBot
Disallow: /

User-agent: SemrushBot
Disallow: /
ROBOTS;

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Get site name from config
     */
    protected function getSiteName(): string
    {
        return config('app.name', 'Bale');
    }
}
