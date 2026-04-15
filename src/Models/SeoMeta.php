<?php

namespace Bale\Seo\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasUuids;

    protected $table = 'seo_meta';

    protected $guarded = ['id'];

    protected $casts = [
        'no_index' => 'boolean',
        'no_follow' => 'boolean',
        'structured_data' => 'array',
    ];

    /**
     * Get the parent seoable model (Post, Page, Section, etc.)
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get robots meta content
     */
    public function getRobotsAttribute(): string
    {
        $robots = [];

        if ($this->no_index) {
            $robots[] = 'noindex';
        } else {
            $robots[] = 'index';
        }

        if ($this->no_follow) {
            $robots[] = 'nofollow';
        } else {
            $robots[] = 'follow';
        }

        return implode(', ', $robots);
    }
}
