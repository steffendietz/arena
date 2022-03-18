<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity()]
class ArenaCharacter
{

    #[Column(type: 'primary')]
    protected int $id;
}
