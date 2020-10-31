<?php

declare(strict_types=1);

return [
    /**
     * Default database connection
     */
    'default'   => 'default',

    'databases' => [
        'default' => [
            'driver' => 'sqlite'
        ],
    ],

    'drivers'   => [
        'sqlite' => [
            'driver'     => \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
            'connection' => 'sqlite:' . directory('root') . 'app.db',
            'profiling'  => true,
        ],
    ]
];
