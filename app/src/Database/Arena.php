<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity(mapper = "Mapper\UuidMapper")
 */
class Arena
{
    /** @Cycle\Column(type = "string(36)", primary = true) */
    protected $uuid;

    public function getUuid(): string
    {
        return $this->uuid;
    }    
}
