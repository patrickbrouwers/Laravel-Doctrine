<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Doctrine Meta Data
    |--------------------------------------------------------------------------
    |
    | Available: annotations|yaml|xml
    |
    */
    'meta' => [
        'driver'  => 'annotations',
        'drivers' => [
            'annotations' => [
                'driver'  => 'annotations',
                'simple'  => false,
                'paths'   => [
                    app_path()
                ],
                'proxies' => [
                    'path' => storage_path('proxies')
                ]
            ],
            'yaml'        => [
                'driver'  => 'yaml',
                'paths'   => [
                    config_path('yaml')
                ],
                'proxies' => [
                    'path' => storage_path('proxies')
                ]
            ],
            'xml'         => [
                'driver'  => 'xml',
                'paths'   => [
                    config_path('xml')
                ],
                'proxies' => [
                    'path' => storage_path('proxies')
                ]
            ]
        ]
    ]
];
