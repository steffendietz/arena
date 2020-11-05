<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Character;
use App\Database\MatchSearch;
use Cycle\ORM\Select\Repository;
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

        /** @var Repository $characterRepo */
        $characterRepo = $this->orm->getRepository(Character::class);

        $characters = $characterRepo
        ->select()
        ->load('matchSearch')
        ->with('user')
        ->where('user.uuid', $user->getUuid())
        ->fetchAll();

        return $this->views->render('character/list.dark.php', [
            'characters' => $characters
        ]);
    }

    public function toggleMatchSearch(string $characterUuid)
    {
        if (($user = $this->auth->getActor()) === null) {
            throw new ForbiddenException();
        }

        /** @var Repository $characterRepo */
        $characterRepo = $this->orm->getRepository(Character::class);

        /** @var Character $character */
        $character =  $characterRepo
            ->select()
            ->load('matchSearch')
            ->with('user')
            ->where('user.uuid', $user->getUuid())
            ->andWhere('uuid', $characterUuid)
            ->fetchOne();

        if ($character !== null) {
            if ($character->isMatchSearching()) {
                $this->tr->delete($character->getMatchSearch());
            } else {
                $this->tr->persist(new MatchSearch($character));
            }
            $this->tr->run();
        }

        return $this->response->redirect('character:list');
    }
}
