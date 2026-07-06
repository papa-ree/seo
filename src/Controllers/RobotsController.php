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

        $disallows = [
            'Disallow: /admin',
            'Disallow: /admin/*',
            'Disallow: /livewire/*',
            'Disallow: /api/*',
        ];

        // Check if debugbar or telescope routes actually exist in the router
        $hasDebugbar = false;
        $hasTelescope = false;
        try {
            $routes = \Illuminate\Support\Facades\Route::getRoutes();
            foreach ($routes as $route) {
                $uri = $route->uri();
                if (str_starts_with($uri, '_debugbar/')) {
                    $hasDebugbar = true;
                }
                if (str_starts_with($uri, 'telescope/')) {
                    $hasTelescope = true;
                }
            }
        } catch (\Throwable $e) {
            // Fallback safe defaults if router is not loaded
        }

        if ($hasDebugbar) {
            $disallows[] = 'Disallow: /_debugbar/*';
        }
        if ($hasTelescope) {
            $disallows[] = 'Disallow: /telescope/*';
        }

        $disallowStr = implode("\n", $disallows);

        $content = <<<ROBOTS
# Robots.txt for {$this->getSiteName()}
# Generated dynamically by Bale

User-agent: *
Allow: /
Allow: /favicon.ico

# Disallow admin and internal paths
{$disallowStr}

# Disallow authentication pages
Disallow: /login
Disallow: /register
Disallow: /password/*

# Allow important resources
Allow: /media/*

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
