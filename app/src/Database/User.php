<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\UuidMapper;
use App\Repository\UserRepository;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity(mapper: UuidMapper::class, repository: UserRepository::class)]
#[Index(columns: ['username'], unique: true)]
class User
{
    #[Column(type: 'string(36)', primary: true)]
    protected string $uuid;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'string')]
    public string $username;

    #[Column(type: 'string')]
    public string $password;

    #[HasMany(target: Character::class)]
    protected ArrayCollection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
