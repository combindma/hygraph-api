<?php

namespace Combindma\HygraphApi;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;
use GraphQL\RawObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HygraphApi
{
    protected string $endpoint;

    protected string $token;

    protected int $ttl;

    protected string $lang;

    public function __construct()
    {
        $this->endpoint = config('hygraph.content_api');
        $this->token = config('hygraph.token');
        $this->ttl = config('hygraph.cache_ttl');
        $this->lang = substr(app()->getLocale(), 0, 2);
    }

    protected function query(Query $query): array|object|null
    {
        $client = new Client($this->endpoint, ['Authorization' => 'Bearer '.$this->token]);
        try {
            return $client->runQuery($query)->getData();
        } catch (QueryError $exception) {
            Log::error($exception->getErrorDetails());
        }

        return null;
    }

    public function pages(?string $lang = null): array|object|null
    {
        $gql = (new Query('pages'))
            ->setArguments(['locales' => new RawObject($lang ?? $this->lang)])
            ->setSelectionSet(
                [
                    'id',
                    'title',
                    'publishedAt',
                    'updatedAt',
                    (new Query('seo'))->setSelectionSet([
                        'title',
                        'description',
                        'noIndex',
                        (new Query('image'))->setSelectionSet(['url']),
                    ]),
                ]
            );

        return $this->query($gql);
    }

    public function page(string $id): array
    {
        return Cache::remember($this->lang.'_'.$id, $this->ttl, function () use ($id) {
            $gql = (new Query('page'))
                ->setArguments(['locales' => new RawObject($this->lang), 'where' => new RawObject('{id: "'.$id.'"}')])
                ->setSelectionSet(
                    [
                        'id',
                        'title',
                        'publishedAt',
                        'updatedAt',
                        (new Query('content'))->setSelectionSet(['html']),
                        (new Query('seo'))->setSelectionSet(['title', 'description', 'noIndex', (new Query('image'))->setSelectionSet(['url'])]),
                    ]
                );
            $page = $this->query($gql)->page;

            return [
                'id' => $page->id,
                'title' => $page->title,
                'content' => $page->content?->html,
                'meta_title' => $page->seo->title,
                'meta_description' => $page->seo->description,
                'noIndex' => $page->seo->noIndex,
                'seo_image' => $page->seo->image?->url,
            ];
        });
    }

    public function options(): object|null
    {
        return Cache::remember('options', $this->ttl, function () {
            $gql = (new Query('settings'))
                ->setSelectionSet(
                    [
                        'id',
                        'option',
                        'value',
                    ]
                );
            $settings = $this->query($gql)->settings;
            $array = [];
            foreach ($settings as $setting) {
                $array[$setting->option] = $setting->value;
            }

            return (object) $array;
        });
    }

    public function optionsAsArray(): array|null
    {
        return Cache::remember('options-as-array', $this->ttl, function () {
            $gql = (new Query('settings'))
                ->setSelectionSet(
                    [
                        'id',
                        'option',
                        'value',
                    ]
                );
            $settings = $this->query($gql)->settings;
            $array = [];
            foreach ($settings as $setting) {
                $array[$setting->option] = $setting->value;
            }

            return $array;
        });
    }

    public function redirects(): array
    {
        return Cache::remember('redirects', $this->ttl, function () {
            $gql = (new Query('redirects'))
                ->setSelectionSet(
                    [
                        'newUrl',
                        'oldUrl',
                        'statusUrl',
                    ]
                );
            $redirects = $this->query($gql)->redirects;
            $array = [];
            foreach ($redirects as $redirect) {
                $array[$redirect->oldUrl] = [$redirect->newUrl, $redirect->statusUrl];
            }

            return $array;
        });
    }

    public function posts(?string $lang = null): array|Collection|null
    {
        return Cache::remember('allPosts', $this->ttl, function () {
            $gql = (new Query('posts'))
                ->setArguments(['orderBy' => new RawObject('publicationDate_DESC')])
                ->setSelectionSet(
                    [
                        'id',
                        'title',
                        'slug',
                        'readingTime',
                        'excerpt',
                        'publicationDate',
                        'modificationDate',
                        (new Query('coverImage'))->setSelectionSet(['url']),
                        (new Query('categories'))->setSelectionSet(['name', 'slug']),
                    ]
                );

            return collect($this->query($gql)->posts);
        });
    }

    public function categories(): array|null
    {
        return Cache::remember('allPostCategories', $this->ttl, function () {
            $gql = (new Query('categories'))
                ->setArguments(['orderBy' => new RawObject('name_ASC')])
                ->setSelectionSet(['id', 'name', 'slug']);

            return $this->query($gql)->categories;
        });
    }

    public function article(string $slug): object|null
    {
        return Cache::remember('article_'.$slug, $this->ttl, function () use ($slug) {
            $gql = (new Query('post'))
                ->setArguments(['where' => new RawObject('{slug: "'.$slug.'"}')])
                ->setSelectionSet(
                    [
                        'id',
                        'title',
                        'slug',
                        'excerpt',
                        'readingTime',
                        'publicationDate',
                        'modificationDate',
                        'tags',
                        (new Query('coverImage'))->setSelectionSet(['url']),
                        (new Query('content'))->setSelectionSet(['html']),
                        (new Query('categories'))->setSelectionSet(['id', 'name', 'slug']),
                        (new Query('author'))
                            ->setSelectionSet(
                                [
                                    'id',
                                    'name',
                                    'title',
                                    'biography',
                                    'instagram',
                                    'linkedin',
                                    'facebook',
                                    'twitter',
                                    (new Query('picture'))->setSelectionSet(['url']),
                                ]
                            ),
                        (new Query('seo'))->setSelectionSet(['title', 'description', (new Query('image'))->setSelectionSet(['url'])]),
                        (new Query('relatedPosts'))->setSelectionSet(['id']),
                        (new Query('toc'))->setSelectionSet([
                            'title',
                            'slug',
                            (new Query('tocLinks'))->setSelectionSet(['title', 'slug']),
                        ]),
                    ]
                );

            if (empty($this->query($gql)->post)) {
                abort('404');
            }

            return $this->query($gql)->post;
        });
    }

    public function featuredPosts(): array|null
    {
        return Cache::remember('featuredPosts', $this->ttl, function () {
            $gql = (new Query('posts'))
                ->setArguments(['where' => new RawObject('{isFeatured: true}'), 'orderBy' => new RawObject('publicationDate_DESC')])
                ->setSelectionSet(
                    [
                        'id',
                        'title',
                        'slug',
                        'readingTime',
                        'excerpt',
                        'publicationDate',
                        (new Query('coverImage'))->setSelectionSet(['url']),
                        (new Query('categories'))->setSelectionSet(['name', 'slug']),
                    ]
                );

            return $this->query($gql)->posts;
        });
    }

    public function relatedPosts(array $ids): array|null
    {
        $gql = (new Query('posts'))
            ->setArguments(['where' => new RawObject('{id_in: ["'.implode('","', $ids).'"]}')])
            ->setSelectionSet(
                [
                    'id',
                    'title',
                    'slug',
                    'readingTime',
                    'excerpt',
                    'publicationDate',
                    'modificationDate',
                    (new Query('coverImage'))->setSelectionSet(['url']),
                    (new Query('categories'))->setSelectionSet(['name', 'slug']),
                ]
            );

        return $this->query($gql)->posts;
    }
}
