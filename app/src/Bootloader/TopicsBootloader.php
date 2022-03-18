<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Database\User;
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
        $ws->authorizeTopic(
            'channel',
            function () {
                return true;
            }
        );
        $ws->authorizeTopic('channel.{uuid}', function ($uuid, AuthScope $auth): bool {
            dumprr('join request');
            /** @var User $user */
            $user = $auth->getActor();
            return $user !== null && $user->getUuid() === $uuid;
        });
    }
}
