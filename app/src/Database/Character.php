<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\UuidMapper;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(mapper: UuidMapper::class)]
class Character
{
    #[Column(type: 'string(36)', primary: true)]
    protected string $uuid;

    #[Column(type: 'string(32)')]
    protected string $name;

    #[Column(type: 'integer')]
    protected int $currentHealth = 100;

    #[HasOne(target: MatchSearch::class, nullable: true, load: 'eager')]
    protected ?MatchSearch $matchSearch;

    #[BelongsTo(target: User::class, inverse: new Inverse('characters', 'hasMany'))]
    protected User $user;

    #[RefersTo(target: Arena::class, nullable: true)]
    protected ?Arena $currentArena;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCurrentHealth(int $currentHealth): void
    {
        $this->currentHealth = $currentHealth;
    }

    public function getCurrentHealth(): int
    {
        return $this->currentHealth;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isMatchSearching(): bool
    {
        return $this->matchSearch !== null;
    }

    public function setMatchSearch(MatchSearch $matchSearch)
    {
        $this->matchSearch = $matchSearch;
    }

    public function getMatchSearch(): ?MatchSearch
    {
        return $this->matchSearch;
    }

    public function setCurrentArena(?Arena $arena): void
    {
        $this->currentArena = $arena;
    }
}
