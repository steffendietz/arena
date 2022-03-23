<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\UuidMapper;
use App\Repository\ArenaRepository;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Collection\Pivoted\PivotedCollection;

#[Entity(mapper: UuidMapper::class, repository: ArenaRepository::class)]
class Arena
{
    #[Column(type: 'string(36)', primary: true)]
    protected string $uuid;

    #[Column(type: 'boolean')]
    protected bool $active = true;

    #[Column(type: 'integer')]
    protected int $levels = 1;

    #[Column(type: 'integer')]
    protected int $currentLevel = 0;

    #[ManyToMany(target: Character::class, through: ArenaCharacter::class)]
    protected PivotedCollection $characters;

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

    public function  isActive(): bool
    {
        return $this->active;
    }

    public function getLevels(): int
    {
        return $this->levels;
    }

    public function getCurrentLevel(): int
    {
        return $this->currentLevel;
    }

    public function setCurrentLevel(int $currentLevel): void
    {
        $this->currentLevel = $currentLevel;
    }

    public function addCharacter(Character $character): void
    {
        $this->characters->add($character);
        $character->setCurrentArena($this);
    }
}
