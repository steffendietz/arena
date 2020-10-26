<?php

declare(strict_types=1);

namespace App\Repository;

use Cycle\ORM\Select\Repository;
use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\TokenInterface;

class UserRepository extends Repository implements ActorProviderInterface 
{
    public function getActor(TokenInterface $token): ?object
    {
        if (!isset($token->getPayload()['userID'])) {
            return null;
        }

        return $this->findByPK($token->getPayload()['userID']);
    }
}
