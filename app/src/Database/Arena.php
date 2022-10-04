<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\UuidMapper;
use App\Repository\ArenaRepository;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Collection\Pivoted\PivotedCollection;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

#[Entity(mapper: UuidMapper::class, repository: ArenaRepository::class)]
#[Behavior\CreatedAt(
    field: 'createdAt',   // Required. By default 'createdAt'
    column: 'created_at'  // Optional. By default 'null'. If not set, will be used information from property declaration.
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',   // Required. By default 'updatedAt'
    column: 'updated_at'  // Optional. By default 'null'. If not set, will be used information from property declaration.
)]
class Arena
{
    #[Column(type: 'string(36)', primary: true)]
    protected string $uuid;

    #[Column(type: 'datetime')]
    private readonly DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

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
     * @return Collection<Character>
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isActive(): bool
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
