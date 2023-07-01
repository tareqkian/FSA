<?php

return [
    'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ]
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\FsaAdmin::class,
        ]
    ]
];
