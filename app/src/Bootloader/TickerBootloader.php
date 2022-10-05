<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Dispatcher\TickerDispatcher;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\KernelInterface;
use Spiral\Broadcasting\Bootloader\BroadcastingBootloader;

final class TickerBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        BroadcastingBootloader::class
    ];

    public function init(KernelInterface $kernel, TickerDispatcher $ticker): void
    {
        $kernel->addDispatcher($ticker);
    }
}
