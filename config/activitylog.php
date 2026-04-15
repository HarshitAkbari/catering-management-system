<?php

return [
    // Named routes to exclude entirely
    'exclude_route_names' => [
        'login',
        'logout',
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
    ],

    // Exact path exclusions (leading slash)
    'exclude_paths' => [
        '/login',
        '/logout',
        '/up',
    ],

    // Prefix exclusions for APIs, telescope, etc. (leading slash)
    'exclude_prefixes' => [
        '/api',
        '/telescope',
        '/horizon',
        '/vendor',
    ],
];

