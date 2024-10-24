<?php
return [
    'general' => [
        'CACHE_EXPIRY' => env('CACHE_EXPIRY', 600),
    ],
    'product' => [
        'default_cache_tag_prefix' => 'Product:',
        'default_cache_time'       => 60 * 10,
    ],
    'order' => [
        'default_cache_tag_prefix' => 'Order:',
        'default_cache_time'       => 60 * 10,
    ],
    'user' => [
        'default_cache_tag_prefix' => 'User:',
        'default_cache_time'       => 60 * 10,
    ],
    'payment' => [
        'default_cache_tag_prefix' => 'Payment:',
        'default_cache_time'       => 60 * 10,
    ],
];
