<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Arena;
use Cycle\ORM\Select\Repository;
use DateTimeImmutable;

class ArenaRepository extends Repository
{
    /**
     * @return Arena[]
     */
    public function findActiveArenas(int $limit = null): array
    {
        $past = new DateTimeImmutable('-1 minute');
        return $this
            ->select()
            ->load('characters')
            ->where([
                'active' => true,
                '@or' => [
                    ['updatedAt' => null],
                    ['updatedAt' => ['>' => $past]],
                ],
            ])
            ->limit($limit)
            ->fetchAll();
    }
}
