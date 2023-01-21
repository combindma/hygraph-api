<?php

namespace Combindma\HygraphApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HygraphApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('hygraph-api')
            ->hasConfigFile('hygraph');
    }

    public function registeringPackage()
    {
        $this->app->singleton(HygraphApi::class, function ($app) {
            return new HygraphApi();
        });

        $this->app->singleton(SeoHelper::class, function ($app) {
            return new SeoHelper();
        });
    }
}
