<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Character;
use Cycle\ORM\TransactionInterface;
use Spiral\Http\Exception\ClientException\ForbiddenException;
use Spiral\Prototype\Traits\PrototypeTrait;

class CharacterController
{
    use PrototypeTrait;

    protected $tr;

    public function __construct(TransactionInterface $tr)
    {
        $this->tr = $tr;
    }

    public function generate(string $name)
    {
        if (($user = $this->auth->getActor()) === null) {
            throw new ForbiddenException();
        }

        $character = new Character($user);
        $character->setName($name);

        $this->tr->persist($character);
        $this->tr->run();

        return sprintf('Generated %s %s', $character->getUuid(), $character->getName());
    }

    public function list()
    {
        if (($user = $this->auth->getActor()) === null) {
            throw new ForbiddenException();
        }

        $characterRepo = $this->orm->getRepository(Character::class);

        return $this->views->render('character/list.dark.php', [
            'characters' => $characterRepo->findAll([
                'user_uuid' => $user->getUuid()
            ])
        ]);
    }
}
