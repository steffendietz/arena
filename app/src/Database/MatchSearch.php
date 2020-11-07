<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation as Cycle;
use DateTimeImmutable;

/**
 * @Cycle\Entity(
 *     repository = "App\Repository\MatchSearchRepository"
 * )
 */
class MatchSearch
{

    /** @Cycle\Column(type = "primary") */
    protected $id;

    /** @Cycle\Column(type = "datetime") */
    protected $started;

    /** @BelongsTo(target = "Character", inverse = @Inverse(as = "matchSearch", type = "hasOne")) */
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
