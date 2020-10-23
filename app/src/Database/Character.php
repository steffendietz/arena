<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity()
 */
class Character
{
    /** @Cycle\Column(type = "primary") */
    protected $id;

    /** @Cycle\Column(type = "string(32)") */
    protected $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }
}
