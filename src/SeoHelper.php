<?php

namespace Combindma\HygraphApi;

use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;

class SeoHelper
{
    public function page(array $page, bool $noindex = false): void
    {
        if ($page['noIndex']) {
            SEOTools::metatags()->addMeta('robots', 'noindex');
        }

        SEOTools::setTitle($page['meta_title']);
        SEOTools::setDescription($page['meta_description']);

        if (! empty($page['seo_image'])) {
            SEOTools::addImages([$page['seo_image']]);
        }

        SEOTools::jsonLd()->setType('WebPage');
        SEOTools::jsonLd()->addValue('@id', url()->current().'#webpage');
        JsonLdMulti::addValue('@id', url()->current().'#webpage');
        JsonLdMulti::addValue('isPartOf', [
            '@type' => 'WebSite',
            'url' => url('/'),
            'name' => config('app.name'),
            'description' => $page['meta_description'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                '@id' => route('home').'#organization',
                'logo' => asset('logo.svg'),
            ],
        ]);
        JsonLdMulti::addValue('logo', [
            '@type' => 'ImageObject',
            '@id' => url()->current().'#logo',
            'url' => asset('logo.svg'),
            'caption' => config('app.name'),
        ]);
    }

    public function article($post): void
    {
        $article = [
            'title' => $post->seo?->title ?? $post->title,
            'description' => $post->seo?->description ?? $post->excerpt,
            'image' => $post->seo?->image?->url ?? $post->coverImage?->url,
            'published_time' => $post->publicationDate,
            'modified_time' => $post->modificationDate,
            'author' => $post->author->name,
        ];
        SEOTools::setTitle($article['title']);
        SEOTools::setDescription($article['description']);

        SEOTools::opengraph()->setTitle($article['title'])
            ->setDescription($article['description'])
            ->setType('article')
            ->setArticle([
                'locale' => 'fr-FR',
                'published_time' => $article['published_time'],
                'modified_time' => $article['modified_time'],
                'author' => $article['author'],
            ]);
        SEOTools::opengraph()->addProperty('locale', 'fr-fr');
        SEOTools::opengraph()->addImage($article['image']);

        SEOTools::jsonLd()->setTitle($article['title']);
        SEOTools::jsonLd()->setDescription($article['description']);
        SEOTools::jsonLd()->setType('Article');
        SEOTools::jsonLd()->addImage($article['image']);
        SEOTools::jsonLd()->addValue('author', [
            '@type' => 'Person',
            'name' => $article['author'],
        ]);
        SEOTools::jsonLd()->addValue('datePublished', $article['published_time']);
        SEOTools::jsonLd()->addValue('dateModified', $article['modified_time']);
        SEOTools::jsonLd()->addValue('headline', $article['title']);
        SEOTools::jsonLd()->addValue('mainEntityOfPage', [
            '@type' => 'WebPage',
            '@id' => url()->current(),
        ]);
        SEOTools::jsonLd()->addValue('publisher', [
            '@type' => 'Organization',
            'name' => config('app.name'),
            '@id' => route('home'),
            'logo' => url('logo.svg'),
        ]);
    }
}
