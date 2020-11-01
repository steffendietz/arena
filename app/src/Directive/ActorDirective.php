<?php

declare(strict_types=1);

namespace App\Directive;

use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Stempler\Directive\AbstractDirective;
use Spiral\Stempler\Node\Dynamic\Directive;

class ActorDirective extends AbstractDirective
{
    use PrototypeTrait;

    public function renderIfLoggedIn(Directive $directice)
    {
        return '<?php if($this->container->get(\Spiral\Auth\AuthScope::class)->getActor() !== null): ?>';
    }

    public function renderActor(Directive $directice)
    {
        return '<?= $this->container->get(\Spiral\Auth\AuthScope::class)->getActor() !== null ? $this->container->get(\Spiral\Auth\AuthScope::class)->getActor()->name : "GUEST" ?>';
    }
}
