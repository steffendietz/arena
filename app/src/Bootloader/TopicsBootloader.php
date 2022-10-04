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
        $ws->authorizeTopic('channel', fn(): bool => true);
        $ws->authorizeTopic('channel.{uuid}', fn($uuid, AuthContextInterface $authContext): bool => $authContext->getActor()?->getUuid() === $uuid);
    }
}
