<?php

namespace Combindma\HygraphApi\Tests;

use Combindma\HygraphApi\HygraphApiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            HygraphApiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.locale', 'fr');
    }
}
