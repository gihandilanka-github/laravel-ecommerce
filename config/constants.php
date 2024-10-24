<?php
return [
    'general' => [
        'CACHE_EXPIRY' => env('CACHE_EXPIRY', 600),
    ],
    'product' => [
        'default_cache_tag_prefix' => 'Product:',
        'default_cache_time'       => 60 * 10,
    ],
];
