<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation as Cycle;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Cycle\Entity(
 *     repository = "App\Repository\UserRepository",
 *     mapper = "Mapper\UuidMapper"
 * )
 * @Cycle\Table(indexes = {
 *     @Cycle\Table\Index(columns = {"username"}, unique = true)
 * })
 */
class User
{
    /** @Cycle\Column(type = "string(36)", primary = true) */
    protected $uuid;

    /** @Cycle\Column(type = "string") */
    public $name;

    /** @Cycle\Column(type = "string") */
    public $username;

    /** @Cycle\Column(type = "string") */
    public $password;

    /** @HasMany(target = "Character") */
    protected $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
