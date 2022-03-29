<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Auth\AuthContextInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Http\WebsocketsBootloader;

class TopicsBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        WebsocketsBootloader::class,
    ];

    public function boot(WebsocketsBootloader $ws): void
    {
        $ws->authorizeTopic('channel', function (): bool {
            return true;
        });
        $ws->authorizeTopic('channel.{uuid}', function ($uuid, AuthContextInterface $authContext): bool {
            return $authContext->getActor()?->getUuid() === $uuid;
        });
    }
}
