<?php

namespace Combindma\HygraphApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\HygraphApi\HygraphApi
 */
class HygraphApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hygraph-api';
    }
}
