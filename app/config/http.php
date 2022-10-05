<?php

declare(strict_types=1);

use Spiral\Auth\Middleware\AuthMiddleware;

return [
    'middleware' => [
        AuthMiddleware::class
    ],
];