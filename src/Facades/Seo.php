<?php

namespace Bale\Seo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bale\Seo\Seo
 */
class Seo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Bale\Seo\Seo::class;
    }
}
