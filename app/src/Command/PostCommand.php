<?php

declare(strict_types=1);

namespace App\Command;

use Spiral\Console\Command;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;
use Symfony\Component\Console\Input\InputArgument;

class PostCommand extends Command
{
    protected const NAME = 'post';
    protected const DESCRIPTION = 'Post a message to topic "channel".';
    protected const ARGUMENTS = [
        ['message', InputArgument::REQUIRED]
    ];

    /**
     * Perform command
     */
    protected function perform(BroadcastInterface $broadcast): void
    {
        $broadcast->publish('channel', $this->argument('message'));
    }
}
