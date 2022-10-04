<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Repository\UserRepository;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Auth\AuthBootloader;

final class UserBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        AuthBootloader::class
    ];

    public function init(AuthBootloader $auth): void
    {
        $auth->addActorProvider(UserRepository::class);
    }
}
