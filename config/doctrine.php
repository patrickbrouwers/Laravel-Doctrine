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
    'dev'              => config('app.debug'),
    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | By default the Laravel default database connection is used
    |
    */
    'connections'      => [
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
    'meta'             => [
        'namespace' => 'App',
        'driver'    => 'annotations',
        'drivers'   => [
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
        'proxies'   => [
            'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', false),
            'namespace'     => false
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Gedmo Doctrine Extensions
    |--------------------------------------------------------------------------
    |
    | If you want to use the Doctrine Extensions from Gedmo,
    | you'll have to set this setting to true.
    |
    */
    'gedmo_extensions' => [
        'enabled'      => false,
        'all_mappings' => true
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine Extensions
    |--------------------------------------------------------------------------
    |
    | Enable/disable Doctrine Extensions by adding or removing them from the list
    |
    */
    'extensions'       => [
        Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeleteableExtension::class,
        //Brouwers\LaravelDoctrine\Extensions\Loggable\LoggableExtension::class,
        //Brouwers\LaravelDoctrine\Extensions\Sortable\SortableExtension::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Default repository
    |--------------------------------------------------------------------------
    */
    'repository'       => Doctrine\ORM\EntityRepository::class,
    /*
    |--------------------------------------------------------------------------
    | Enable Debugbar Doctrine query collection
    |--------------------------------------------------------------------------
    */
    'debugbar'         => env('DOCTRINE_DEBUGBAR', false),
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
    'cache'            => [
        'default' => config('cache.default')
    ]
];
