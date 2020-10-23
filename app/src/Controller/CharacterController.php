<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Character;
use Cycle\ORM\TransactionInterface;

class CharacterController
{

    protected $tr;

    public function __construct(TransactionInterface $tr)
    {
        $this->tr = $tr;
    }

    public function generate(string $name)
    {
        $character = new Character;
        $character->setName($name);

        $this->tr->persist($character);
        $this->tr->run();

        return sprintf('Generated %s %s', $character->getId(), $character->getName());
    }
}
