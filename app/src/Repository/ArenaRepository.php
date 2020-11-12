<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Arena;
use Cycle\ORM\Select\Repository;

class ArenaRepository extends Repository
{
    /**
     * @return Arena[]
     */
    public function findActiveArenas(int $limit = null): array
    {
        return $this
            ->select()
            ->load('characters')
            ->where('active', true)
            ->limit($limit)
            ->fetchAll();
    }
}
