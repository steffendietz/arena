<?php

namespace App\Bootloader;

use App\Broadcast\DeferredBroadcast;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\FinalizerInterface;
use Spiral\Core\Container;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;

final class DeferredBroadcastBootloader extends Bootloader
{
    public function boot(Container $container, FinalizerInterface $finalizer): void
    {
        $container->bindSingleton(
            DeferredBroadcast::class,
            function (BroadcastInterface $broadcast): DeferredBroadcast {
                return new DeferredBroadcast($broadcast);
            }
        );

        $finalizer->addFinalizer(function () use ($container) {
            /** @var DeferredBroadcast $deferredBroadcast */
            $deferredBroadcast = $container->get(DeferredBroadcast::class);

            $deferredBroadcast->sendDeferredMessages();
        });
    }
}
