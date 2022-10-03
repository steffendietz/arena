<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\UuidMapper;
use App\Interfaces\IdentifiableInterface;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

#[Entity(mapper: UuidMapper::class)]
class Character implements JsonSerializable, IdentifiableInterface
{
    #[Column(type: 'string(36)', primary: true)]
    protected string $uuid;

    #[Column(type: 'string(32)')]
    protected string $name;

    #[Column(type: 'integer')]
    protected int $currentHealth = 100;

    #[HasOne(target: MatchSearch::class, nullable: true, load: 'eager')]
    protected ?MatchSearch $matchSearch = null;

    #[BelongsTo(target: User::class, nullable: true, inverse: new Inverse('characters', 'hasMany'))]
    protected ?User $user;

    #[RefersTo(target: Arena::class, nullable: true)]
    protected ?Arena $currentArena = null;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

    public function getIdentifier(): string
    {
        return $this->getUuid();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function isMatchSearching(): bool
    {
        return $this->matchSearch !== null;
    }

    public function setMatchSearch(?MatchSearch $matchSearch)
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

    public function getCurrentArena(): ?Arena
    {
        return $this->currentArena;
    }

    #[ArrayShape([
        'id' => "string",
        'name' => "string",
        'health' => "int",
        'isSearching' => "bool",
        'isFighting' => "bool"
    ])]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'health' => $this->currentHealth,
            'isSearching' => $this->isMatchSearching(),
            'isFighting' => !is_null($this->currentArena),
        ];
    }
}
