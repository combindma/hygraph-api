<?php

return [
    'content_api' => env('HYPGRAPH_CONTENT_API', ''),
    'token' => env('HYPGRAPH_TOKEN', ''),
    'cache_ttl' => env('HYPGRAPH_CACHE_TTL', 60 * 60 * 24 * 30),
];
