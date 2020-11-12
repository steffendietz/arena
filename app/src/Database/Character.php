<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity(
 *     mapper = "Mapper\UuidMapper"
 * )
 */
class Character
{
    /** @Cycle\Column(type = "string(36)", primary = true) */
    protected $uuid;

    /** @Cycle\Column(type = "string(32)") */
    protected $name;

    /** @HasOne(target = "MatchSearch", load = "eager") */
    protected $matchSearch;

    /** @BelongsTo(target = "User", inverse = @Inverse(as = "characters", type = "hasMany")) */
    protected $user;

    /** @RefersTo(target = "Arena", nullable = true) */
    protected $currentArena;

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
