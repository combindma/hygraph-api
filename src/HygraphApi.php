<?php

namespace Combindma\HygraphApi;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;
use GraphQL\RawObject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HygraphApi
{
    protected string $endpoint;

    protected string $token;

    protected int $ttl;

    protected string $defaultLocale;

    protected string $lang;

    public function __construct()
    {
        $this->endpoint = config('hygraph.content_api');
        $this->token = config('hygraph.token');
        $this->ttl = config('hygraph.cache_ttl');
        $this->defaultLocale = config('app.locale');
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
                        (new Query('seo'))->setSelectionSet(['title', 'description', 'noIndex', (new Query('image'))->setArguments(['locales' => new RawObject($this->defaultLocale)])->setSelectionSet(['url'])]),
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

    public function options(): ?object
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

    public function optionsAsArray(): ?array
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
}
