<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Database\User;
use Spiral\Auth\AuthScope;
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
        $ws->authorizeTopic('channel.{uuid}', function ($uuid, AuthScope $auth): bool {
            /** @var User $user */
            $user = $auth->getActor();
            if ($user !== null && $user->getUuid() === $uuid) {
                return true;
            }
        });
    }
}
