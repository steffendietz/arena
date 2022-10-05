<?php

declare(strict_types=1);

use Spiral\Auth\AuthContextInterface;
use Spiral\Core\Container\Autowire;
use Spiral\RoadRunnerBridge\Broadcasting\RoadRunnerBroadcast;
use Spiral\RoadRunnerBridge\Broadcasting\RoadRunnerGuard;

return [
    'default' => env('BROADCAST_CONNECTION', 'roadrunner'),
    'authorize' => [
        'path' => '/ws',
        'topics' => [
            'channel' => fn(): bool => true,
            'channel.{uuid}' => fn($uuid, AuthContextInterface $authContext): bool => $authContext->getActor()?->getUuid() === $uuid
        ],
    ],
    'connections' => [
        'roadrunner' => [
            'driver' => 'roadrunner',
            'guard' => Autowire::wire(RoadRunnerGuard::class),
        ]
    ],
    'driverAliases' => [
        'roadrunner' => RoadRunnerBroadcast::class,
    ],
];
