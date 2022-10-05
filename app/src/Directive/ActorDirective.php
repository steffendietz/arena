<?php

declare(strict_types=1);

namespace App\Directive;

use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Stempler\Directive\AbstractDirective;
use Spiral\Stempler\Node\Dynamic\Directive;

class ActorDirective extends AbstractDirective
{
    use PrototypeTrait;

    public function renderIfLoggedIn(Directive $directive)
    {
        return '<?php if($this->container->get(\Spiral\Auth\AuthScope::class)->getActor() !== null): ?>';
    }

    public function renderActor(Directive $directive)
    {
        return '<?= $this->container->get(\Spiral\Auth\AuthScope::class)->getActor()?->name ?? "GUEST" ?>';
    }

    public function renderActorUuid(Directive $directive)
    {
        return '<?= $this->container->get(\Spiral\Auth\AuthScope::class)->getActor()?->getUuid() ?>';
    }

    public function renderActorUuidJs(Directive $directive)
    {
        return '<?= $this->container->get(\Spiral\Auth\AuthScope::class)->getActor() !== null ? \'\\\'\' . $this->container->get(\Spiral\Auth\AuthScope::class)->getActor()->getUuid() . \'\\\'\'  : \'null\' ?>';
    }
}
