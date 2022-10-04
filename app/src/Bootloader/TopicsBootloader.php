<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Auth\AuthContextInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Broadcasting\Bootloader\BroadcastingBootloader;
use Spiral\Broadcasting\TopicRegistryInterface;

final class TopicsBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        BroadcastingBootloader::class,
    ];

    public function init(TopicRegistryInterface $topicRegistry): void
    {
        $topicRegistry->register('channel', fn(): bool => true);
        $topicRegistry->register(
            'channel.{uuid}',
            fn($uuid, AuthContextInterface $authContext): bool => $authContext->getActor()?->getUuid() === $uuid
        );
    }
}
