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
}
