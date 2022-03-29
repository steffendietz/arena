<?php

namespace App\Database\Mapper;

use Cycle\ORM\Mapper\Mapper;
use Ramsey\Uuid\Uuid;

class UuidMapper extends Mapper
{
    public function nextPrimaryKey(): ?array
    {
        return [
            'uuid' => Uuid::uuid4()->toString()
        ];
    }
}
