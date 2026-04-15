<?php

namespace Bale\Seo\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SitemapController extends Controller
{
    /**
     * Generate sitemap index
     */
    public function index(): Response
    {
        $baseUrl = url('/');
        $postModel = config('seo.sitemap.models.post');
        $pageModel = config('seo.sitemap.models.page');

        $sitemaps = [];

        if ($postModel && class_exists($postModel)) {
            $sitemaps[] = [
                'loc' => url('/sitemap-posts.xml'),
                'lastmod' => $postModel::where('published', true)->latest('updated_at')->value('updated_at'),
            ];
        }

        if ($pageModel && class_exists($pageModel)) {
            $sitemaps[] = [
                'loc' => url('/sitemap-pages.xml'),
                'lastmod' => $pageModel::latest('updated_at')->value('updated_at'),
            ];
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . view('seo::sitemap.index', compact('sitemaps', 'baseUrl'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate posts sitemap
     */
    public function posts(): Response
    {
        $postModel = config('seo.sitemap.models.post');
        
        $posts = [];
        if ($postModel && class_exists($postModel)) {
            $posts = $postModel::where('published', true)
                ->select(['id', 'slug', 'title', 'thumbnail', 'updated_at'])
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . view('seo::sitemap.posts', compact('posts'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate pages sitemap
     */
    public function pages(): Response
    {
        $pageModel = config('seo.sitemap.models.page');

        $pages = [];
        if ($pageModel && class_exists($pageModel)) {
            $pages = $pageModel::select(['id', 'slug', 'title', 'updated_at'])
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . view('seo::sitemap.pages', compact('pages'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
