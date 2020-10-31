<?php
/**
 * {project-name}
 * 
 * @author {author-name}
 */
declare(strict_types=1);

namespace App\Bootloader;

use App\Directive\ActorDirective;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Stempler\Bootloader\StemplerBootloader;

class DirectiveBootloader extends Bootloader
{
    protected const BINDINGS = [];

    protected const SINGLETONS = [];

    protected const DEPENDENCIES = [
        StemplerBootloader::class
    ];

    public function boot(StemplerBootloader $stempler): void
    {
        $stempler->addDirective(ActorDirective::class);
    }
}
