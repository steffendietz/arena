<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity()
 */
class ArenaCharacter
{

    /** @Cycle\Column(type = "primary") */
    protected $id;
}
