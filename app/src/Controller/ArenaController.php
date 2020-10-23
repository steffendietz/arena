<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Arena;
use Cycle\ORM\TransactionInterface;

class ArenaController
{

    protected $tr;

    public function __construct(TransactionInterface $tr)
    {
        $this->tr = $tr;
    }

    public function generate(): string
    {
        $arena = new Arena();

        $this->tr->persist($arena);
        $this->tr->run();

        return sprintf('Generated %s!', $arena->getUuid());
    }
}
