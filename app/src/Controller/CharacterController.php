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

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generate(string $name)
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
            $characters[] = $this->mapCharacterToJson($character);
        }
        return $this->response->json(
            $characters,
            200
        );
    }

    /**
     * maps a Character to the FEs JSON representation
     */
    private function mapCharacterToJson(Character $character): array
    {
        return [
            'id' => $character->getUuid(),
            'name' => $character->getName(),
            'isSearching' => !is_null($character->getMatchSearch()),
            'isFighting' => !is_null($character->getCurrentArena()),
        ];
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

        $characters = $characterRepo
            ->select()
            ->load('matchSearch')
            ->with('user')
            ->where('user.uuid', $user->getUuid())
            ->fetchAll();

        return $characters;
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
            $this->mapCharacterToJson($character),
            200
        );
    }
}
