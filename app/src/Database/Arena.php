<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Relation\Pivoted\PivotedCollection;

/**
 * @Cycle\Entity(
 *     repository = "App\Repository\ArenaRepository",
 *     mapper = "Mapper\UuidMapper"
 * )
 */
class Arena
{
    /** @Cycle\Column(type = "string(36)", primary = true) */
    protected $uuid;

    /** @Cycle\Column(type = "boolean") */
    protected $active = true;

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

    /**
     * @return Character[]
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function addCharacter(Character $character): void
    {
        $this->characters->add($character);
        $character->setCurrentArena($this);
    }
}
