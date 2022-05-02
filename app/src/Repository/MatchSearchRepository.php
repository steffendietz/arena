<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\MatchSearch;
use Cycle\ORM\Select\Repository;

class MatchSearchRepository extends Repository
{
    /**
     * @return MatchSearch[]
     */
    public function findOldestMatchSearches($limit = 100): array
    {
        return $this->select()
            ->load('character.user')
            ->orderBy('started', 'DESC')
            ->limit($limit)
            ->fetchAll();
    }
}
