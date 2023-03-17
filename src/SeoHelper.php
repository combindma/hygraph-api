<?php

namespace Combindma\HygraphApi;

use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Arr;

class SeoHelper
{
    public function page(array $page): void
    {
        if ($page['noIndex']) {
            SEOTools::metatags()->addMeta('robots', 'noindex');
        }

        if (! empty($page['seo_image'])) {
            SEOTools::addImages([$page['seo_image']]);
        }

        SEOTools::setTitle($page['meta_title'])->setDescription($page['meta_description']);

        OpenGraph::addProperty('local', app()->getLocale());

        JsonLdMulti::setType('WebPage')
            ->addValue('@id', url()->current().'/#webpage');

        $this->addOrganization();
    }

    public function homepage(array $page): void
    {
        config()->set('seotools.meta.defaults.title', false);
        $this->page($page);
        JsonLdMulti::newJsonLd()
            ->addValue('@id', url('/').'/#website')
            ->setType('WebSite')
            ->setUrl(url('/'))
            ->setTitle(config('app.name'))
            ->setDescription('');
        if (! empty($this->sameAs())) {
            JsonLdMulti::addValue('sameAs', $this->sameAs());
        }
    }

    public function article($post): void
    {
        $article = [
            'title' => $post->seo?->title ?? $post->title,
            'description' => $post->seo?->description ?? $post->excerpt,
            'image' => $post->seo?->image?->url ?? $post->coverImage?->url,
            'published_time' => $post->publicationDate,
            'modified_time' => $post->modificationDate,
            'author' => $post->author?->name,
        ];

        SEOTools::setTitle($article['title']);
        SEOTools::setDescription($article['description']);
        SEOTools::addImages([$article['image']]);

        OpenGraph::setType('article')
            ->setArticle([
                'locale' => app()->getLocale(),
                'published_time' => $article['published_time'],
                'modified_time' => $article['modified_time'],
                'author' => $article['author'],
            ])
            ->addProperty('locale', app()->getLocale());

        JsonLdMulti::setType('Article')
            ->setTitle($article['title'])
            ->setDescription($article['description'])
            ->addValue('inLanguage', app()->getLocale())
            ->addValue('headline', $article['title'])
            ->addValue('datePublished', $article['published_time'])
            ->addValue('dateModified', $article['modified_time'])
            ->addValue('image', [
                '@type' => 'ImageObject',
                'url' => $article['image'],
            ])
            ->addValue('author', [
                '@type' => 'Person',
                'name' => $article['author'],
            ])
            ->addValue('mainEntityOfPage', [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ])
            ->addValue('publisher', [
                '@type' => 'Organization',
                'name' => config('app.name'),
                '@id' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('logo.svg'),
                ],
            ]);

        $this->addOrganization();
    }

    protected function addOrganization(): void
    {
        JsonLdMulti::newJsonLd()
            ->addValue('@id', url('/').'/#organization')
            ->setType('Organization')
            ->setUrl(url('/'))
            ->addValue('logo', url('logo.svg'))
            ->setTitle(config('app.name'))
            ->setDescription('');
        if (! empty($this->sameAs())) {
            JsonLdMulti::addValue('sameAs', $this->sameAs());
        }
    }

    protected function sameAs(): array
    {
        return array_values(Arr::only(
            \Combindma\HygraphApi\Facades\HygraphApi::optionsAsArray(),
            ['facebook', 'linkedin', 'instagram', 'twitter', 'pinterest', 'tiktok', 'youtube', 'github']
        ));
    }
}
