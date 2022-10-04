<?php

declare(strict_types=1);

namespace App\Database;

use App\Repository\MatchSearchRepository;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\Inverse;
use DateTimeImmutable;
use DateTimeInterface;

#[Entity(repository: MatchSearchRepository::class)]
class MatchSearch
{
    #[Column(type: 'primary')]
    protected int $id;

    #[Column(type: 'datetime')]
    protected DateTimeInterface $started;

    public function __construct(
        #[BelongsTo(target: Character::class, inverse: new Inverse('matchSearch', 'hasOne'))]
        protected Character $character
    ) {
        $this->started = new DateTimeImmutable();
    }

    public function getStarted(): DateTimeImmutable|DateTimeInterface
    {
        return $this->started;
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }
}
