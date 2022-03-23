<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Auth\AuthScope;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\RoadRunnerBridge\Bootloader\WebsocketsBootloader;

class TopicsBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        WebsocketsBootloader::class
    ];

    public function boot(WebsocketsBootloader $ws): void
    {
        $ws->authorizeTopic('channel', function (): bool {
            return true;
        });
        $ws->authorizeTopic('channel.{uuid}', function ($uuid, AuthScope $auth): bool {
            return $auth->getActor()?->getUuid() === $uuid;
        });
    }
}
