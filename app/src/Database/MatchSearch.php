<?php

declare(strict_types=1);

namespace App\Database;

use App\Repository\MatchSearchRepository;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTimeInterface;
use DateTimeImmutable;

#[Entity(repository: MatchSearchRepository::class)]
class MatchSearch
{
    #[Column(type: 'primary')]
    protected int $id;

    #[Column(type: 'datetime')]
    protected DateTimeInterface $started;

    #[BelongsTo(target: Character::class, inverse: new Inverse('matchSearch', 'hasOne'))]
    protected $character;

    public function __construct(Character $character)
    {
        $this->started = new DateTimeImmutable();
        $this->character = $character;
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }
}
