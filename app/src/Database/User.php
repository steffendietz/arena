<?php

declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity(repository="App\Repository\UserRepository")
 * @Cycle\Table(indexes={
 *     @Cycle\Table\Index(columns={"username"}, unique=true)
 * })
 */
class User
{
    /**
     * @Cycle\Column(type = "primary")
     */
    public $id;

    /** @Cycle\Column(type="string") */
    public $name;

    /** @Cycle\Column(type="string") */
    public $username;

    /** @Cycle\Column(type="string") */
    public $password;
}
