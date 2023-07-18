<?php

namespace Combindma\HygraphApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\HygraphApi\SeoHelper
 *
 * @method page(array $page): void
 * @method homepage(array $page): void
 * @method article($post): void
 */
class SeoHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\HygraphApi\SeoHelper::class;
    }
}
