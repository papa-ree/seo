<?php

namespace Bale\Seo\Traits;

use Bale\Seo\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

/**
 * Trait HasSeoMeta
 * 
 * Add this trait to any model that needs SEO meta support.
 * The model should have 'title' and optionally 'content' attributes.
 */
trait HasSeoMeta
{
    /**
     * Boot the trait
     */
    public static function bootHasSeoMeta(): void
    {
        // Delete SEO meta when model is deleted
        static::deleting(function ($model) {
            $model->seoMeta()->delete();
        });
    }

    /**
     * Get the SEO meta for this model
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Get SEO title (from seo_meta or fallback to model title)
     */
    public function getSeoTitle(): string
    {
        return $this->seoMeta?->title
            ?? $this->title
            ?? '';
    }

    /**
     * Get SEO description (from seo_meta or auto-generate from content)
     */
    public function getSeoDescription(): string
    {
        if ($this->seoMeta?->description) {
            return $this->seoMeta->description;
        }

        // Try to generate from content
        if (method_exists($this, 'getExcerpt')) {
            return $this->getExcerpt(160);
        }

        if (isset($this->content)) {
            $content = $this->content;

            if (is_array($content) && isset($content['blocks'])) {
                $text = collect($content['blocks'])
                    ->map(fn($block) => $block['data']['text'] ?? '')
                    ->implode(' ');
                return Str::limit(strip_tags($text), 160);
            }

            if (is_string($content)) {
                return Str::limit(strip_tags($content), 160);
            }
        }

        return '';
    }

    /**
     * Get Open Graph title
     */
    public function getOgTitle(): string
    {
        return $this->seoMeta?->og_title
            ?? $this->getSeoTitle();
    }

    /**
     * Get Open Graph description
     */
    public function getOgDescription(): string
    {
        return $this->seoMeta?->og_description
            ?? $this->getSeoDescription();
    }

    /**
     * Get Open Graph image
     */
    public function getOgImage(): ?string
    {
        if ($this->seoMeta?->og_image) {
            return $this->seoMeta->og_image;
        }

        // Fallback to thumbnail if exists
        if (isset($this->thumbnail) && $this->thumbnail) {
            // Check if it's already a full URL
            if (str_starts_with($this->thumbnail, 'http')) {
                return $this->thumbnail;
            }

            // Fallback to CDN for thumbnails
            // We keep the dependency on Bale\Emperan\Support\Cdn for now
            // as it is likely present in the "bale" stack.
            if (class_exists('Bale\Emperan\Support\Cdn')) {
                return \Bale\Emperan\Support\Cdn::url('thumbnails/' . $this->thumbnail);
            }
        }

        return null;
    }

    /**
     * Get SEO keywords
     */
    public function getSeoKeywords(): string
    {
        return $this->seoMeta?->keywords ?? '';
    }

    /**
     * Get canonical URL
     */
    public function getCanonicalUrl(): ?string
    {
        return $this->seoMeta?->canonical_url;
    }

    /**
     * Get robots meta content
     */
    public function getSeoRobots(): string
    {
        return $this->seoMeta?->robots ?? 'index, follow';
    }

    /**
     * Get Open Graph type
     */
    public function getOgType(): string
    {
        if (str_contains(get_class($this), 'Post')) {
            return 'article';
        }

        return 'website';
    }

    /**
     * Get Twitter Card type
     */
    public function getTwitterCardType(): string
    {
        return $this->seoMeta?->twitter_card ?? 'summary_large_image';
    }

    /**
     * Get structured data (JSON-LD)
     */
    public function getStructuredData(): ?array
    {
        return $this->seoMeta?->structured_data;
    }

    /**
     * Create or update SEO meta
     */
    public function updateSeoMeta(array $data): SeoMeta
    {
        return $this->seoMeta()->updateOrCreate(
            ['seoable_id' => $this->id, 'seoable_type' => get_class($this)],
            $data
        );
    }
}
