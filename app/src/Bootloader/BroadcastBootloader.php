<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\Container;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Broadcast\Broadcast;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;
use Spiral\RoadRunnerBridge\Bootloader\RoadRunnerBootloader;

final class BroadcastBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        RoadRunnerBootloader::class
    ];

    public function boot(Container $container): void
    {
        $this->registerBroadcast($container);
    }

    private function registerBroadcast(Container $container)
    {
        $container->bind(BroadcastInterface::class, Broadcast::class);
        $container->bindSingleton(
            Broadcast::class,
            fn(RPCInterface $rpc): Broadcast => new Broadcast($rpc)
        );
    }
}
