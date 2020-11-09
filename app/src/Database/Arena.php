<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Relation\Pivoted\PivotedCollection;

/**
 * @Cycle\Entity(mapper = "Mapper\UuidMapper")
 */
class Arena
{
    /** @Cycle\Column(type = "string(36)", primary = true) */
    protected $uuid;

    /** @ManyToMany(target = "Character", though = "ArenaCharacter") */
    protected $characters;

    public function __construct()
    {
        $this->characters = new PivotedCollection();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCharacters()
    {
        return $this->characters;
    }
}
