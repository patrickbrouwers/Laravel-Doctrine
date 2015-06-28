<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development state
    |--------------------------------------------------------------------------
    |
    | If set to false, caching will become active
    |
    */
    'dev'         => config('app.debug'),
    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | By default the Laravel default database connection is used
    |
    */
    'connections' => [
        'default' => config('database.default')
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine Meta Data
    |--------------------------------------------------------------------------
    |
    | Available: annotations|yaml|xml
    |
    */
    'meta'        => [
        'driver'  => 'annotations',
        'drivers' => [
            'annotations' => [
                'driver'  => 'annotations',
                'simple'  => false,
                'paths'   => [
                    app_path()
                ],
                'proxies' => [
                    'path' => storage_path('proxies/annotations')
                ]
            ],
            'yaml'        => [
                'driver'  => 'yaml',
                'paths'   => [
                    config_path('yaml')
                ],
                'proxies' => [
                    'path' => storage_path('proxies/yaml')
                ]
            ],
            'xml'         => [
                'driver'  => 'xml',
                'paths'   => [
                    config_path('xml')
                ],
                'proxies' => [
                    'path' => storage_path('proxies/xml')
                ]
            ]
        ],
        'proxies' => [
            'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', false),
            'namespace'     => false
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine Extensions
    |--------------------------------------------------------------------------
    */
    'extensions'  => [
        Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeleteableExtension::class
    ],
    /*
    |--------------------------------------------------------------------------
    | Default repository
    |--------------------------------------------------------------------------
    */
    'repository'  => Doctrine\ORM\EntityRepository::class,
    /*
    |--------------------------------------------------------------------------
    | Enable Debugbar Doctrine query collection
    |--------------------------------------------------------------------------
    */
    'debugbar'    => env('DOCTRINE_DEBUGBAR', false),
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | By default the Laravel cache setting is used,
    | but it's possible to overrule here
    |
    | Available: acp|array|file|memcached|redis
    |
    */
    'cache'       => [
        'default' => config('cache.default')
    ]
];
