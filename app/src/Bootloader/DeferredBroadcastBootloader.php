<?php

namespace App\Bootloader;

use App\Broadcast\DeferredBroadcast;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\FinalizerInterface;
use Spiral\Broadcasting\BroadcastInterface;
use Spiral\Core\Container;

final class DeferredBroadcastBootloader extends Bootloader
{
    public function init(Container $container, FinalizerInterface $finalizer): void
    {
        $container->bindSingleton(
            DeferredBroadcast::class,
            fn(BroadcastInterface $broadcast): DeferredBroadcast => new DeferredBroadcast($broadcast)
        );

        $finalizer->addFinalizer(function () use ($container) {
            /** @var DeferredBroadcast $deferredBroadcast */
            $deferredBroadcast = $container->get(DeferredBroadcast::class);

            $deferredBroadcast->sendDeferredMessages();
        });
    }
}
