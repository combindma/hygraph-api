<?php

namespace Combindma\HygraphApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\HygraphApi\SeoHelper
 */
class SeoHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\HygraphApi\SeoHelper::class;
    }
}
