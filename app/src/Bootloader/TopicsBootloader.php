<?php

declare(strict_types=1);

namespace App\Bootloader;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Auth\AuthScope;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Auth\AuthBootloader;
use Spiral\Bootloader\Http\WebsocketsBootloader;
use Spiral\Security\Actor\Actor;

class TopicsBootloader extends Bootloader
{

    protected $auth;

    protected const DEPENDENCIES = [
        AuthBootloader::class,
        WebsocketsBootloader::class
    ];

    public function boot(WebsocketsBootloader $ws, AuthBootloader $auth): void
    {
        $this->auth = $auth;
        $ws->authorizeTopic(
            'channel',
            function () {
                return true;
            }
        );
        $ws->authorizeTopic('channel.{uuid}', [$this, 'authorizeUserTopic']);
    }

    public function authorizeUserTopic($uuid, AuthScope $test, ServerRequestInterface $test2): bool
    {
        dumprr($test2->getCookieParams());
        return true;
    }
}
