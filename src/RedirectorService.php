<?php

namespace Combindma\HygraphApi;

use Spatie\MissingPageRedirector\Redirector\Redirector;
use Symfony\Component\HttpFoundation\Request;

class RedirectorService implements Redirector
{
    public function __construct(public HygraphApi $hygraphApi)
    {
    }

    public function getRedirectsFor(Request $request): array
    {
        return $this->hygraphApi->redirects();
    }
}
