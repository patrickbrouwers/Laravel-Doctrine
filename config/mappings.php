<?php

return [

    'App\User' => [
        'table'  => 'users',
        'fields' => [
            'id'   => [
                'type'     => 'integer',
                'strategy' => 'identity'
            ],
            'name' => [
                'type'     => 'string',
                'nullable' => false,
            ]
        ]
    ]

];
