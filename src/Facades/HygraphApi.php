<?php

namespace Combindma\HygraphApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\HygraphApi\HygraphApi
 */
class HygraphApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\HygraphApi\HygraphApi::class;
    }
}
