<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Character;
use App\Database\MatchSearch;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Select\Repository;
use Spiral\Http\Exception\ClientException\ForbiddenException;
use Spiral\Prototype\Traits\PrototypeTrait;

class CharacterController
{
    use PrototypeTrait;

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generate(string $name): string
    {
        if (($user = $this->auth->getActor()) === null) {
            throw new ForbiddenException();
        }

        $character = new Character($user);
        $character->setName($name);

        $this->entityManager->persist($character);
        $this->entityManager->run();

        return sprintf('Generated %s %s', $character->getUuid(), $character->getName());
    }

    public function list()
    {
        return $this->views->render('character/list.dark.php', [
            'characters' => $this->getCharacters(),
        ]);
    }

    public function getList()
    {
        $characters = [];
        foreach ($this->getCharacters() as $character) {
            $characters[] = $character;
        }
        return $this->response->json(
            $characters,
            200
        );
    }

    /**
     * @return Character[]
     */
    private function getCharacters(): array
    {
        if (($user = $this->auth->getActor()) === null) {
            throw new ForbiddenException();
        }

        /** @var Repository $characterRepo */
        $characterRepo = $this->orm->getRepository(Character::class);

        return $characterRepo
            ->select()
            ->load('matchSearch')
            ->with('user')
            ->where('user.uuid', $user->getUuid())
            ->fetchAll();
    }

    public function postToggleMatchSearch(string $id)
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
            ->andWhere('uuid', $id)
            ->fetchOne();

        if ($character !== null) {
            if ($character->isMatchSearching()) {
                $character->setMatchSearch(null);
            } else {
                $character->setMatchSearch(new MatchSearch($character));
            }
            $this->entityManager->persistState($character);
            $this->entityManager->run();
        }

        return $this->response->json(
            $character,
            200
        );
    }
}
