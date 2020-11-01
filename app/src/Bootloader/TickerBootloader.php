<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Dispatcher\TickerDispatcher;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\KernelInterface;

class TickerBootloader extends Bootloader
{
    public function boot(KernelInterface $kernel, TickerDispatcher $ticker): void
    {
        $kernel->addDispatcher($ticker);
    }
}
