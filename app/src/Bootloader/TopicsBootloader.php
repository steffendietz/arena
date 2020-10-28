<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Http\WebsocketsBootloader;

class TopicsBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        WebsocketsBootloader::class
    ];

    public function boot(WebsocketsBootloader $ws): void
    {
        $ws->authorizeTopic(
            'channel',
            function () {
                return true;
            }
        );
    }
}
