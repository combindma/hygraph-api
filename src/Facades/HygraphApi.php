<?php

namespace Combindma\HygraphApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\HygraphApi\HygraphApi
 *
 * @method pages(?string $lang): array|object|null
 * @method page(string $id): array
 * @method options(): object|null
 * @method optionsAsArray(): array|null
 * @method redirects(): array
 * @method announcements(?string $lang = null): array|Collection|null
 * @method posts(?string $lang = null): array|Collection|null
 * @method categories(): array|null
 * @method article(string $slug): object|null
 * @method featuredPosts(): array|null
 * @method relatedPosts(array $ids): array|null
 */
class HygraphApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\HygraphApi\HygraphApi::class;
    }
}
