<?php

declare(strict_types=1);

namespace App\Dispatcher;

use App\Database\Arena;
use App\Database\MatchSearch;
use App\Database\User;
use App\Repository\ArenaRepository;
use App\Repository\MatchSearchRepository;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\Roadrunner\Broadcast\BroadcastInterface;
use Spiral\RoadRunner\Worker;

class TickerDispatcher implements DispatcherInterface
{

    const CHARACTERS_NEEDED_FOR_MATCH = 3;

    private ORMInterface $orm;
    private EntityManagerInterface $entityManager;
    private EnvironmentInterface $env;
    private BroadcastInterface $broadcast;
    private FinalizerInterface $finalizer;
    private ContainerInterface $container;

    public function __construct(
        ORMInterface $orm,
        EntityManagerInterface $entityManager,
        EnvironmentInterface $env,
        BroadcastInterface $broadcast,
        FinalizerInterface $finalizer,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->orm = $orm;
        $this->env = $env;
        $this->broadcast = $broadcast;
        $this->finalizer = $finalizer;
        $this->container = $container;
    }

    public function canServe(): bool
    {
        return php_sapi_name() === 'cli' && $this->env->get('RR_TICKER') !== null;
    }

    public function serve(): void
    {
        /** @var Worker $worker */
        $worker = $this->container->get(Worker::class);

        /** @var MatchSearchRepository $matchSearchRepository */
        $matchSearchRepository = $this->orm->getRepository(MatchSearch::class);

        while (($body = $worker->receive($ctx)) !== null) {
            $lastTick = json_decode($ctx)->lastTick;
            $numTick = json_decode($body)->tick;

            // do matchmaking
            file_put_contents('match-search.txt', 'MatchSearch ' . $numTick . PHP_EOL, FILE_APPEND);
            $matchSearches = $matchSearchRepository->findOldestMatchSearches(10 * static::CHARACTERS_NEEDED_FOR_MATCH);
            foreach (array_chunk($matchSearches, static::CHARACTERS_NEEDED_FOR_MATCH) as $chunkIndex => $chunk) {
                $matchCount = count($chunk);
                if ($matchCount >= static::CHARACTERS_NEEDED_FOR_MATCH) {
                    // create match
                    $arena = new Arena();
                    foreach ($chunk as $matchSearch) {
                        $character = $matchSearch->getCharacter();

                        // add character to match
                        $arena->addCharacter($character);

                        $characterName = $character->getName();
                        $characterUuid = $character->getUuid();

                        $this->sendToUser($character->getUser(), sprintf('Match ' . $chunkIndex . ' found for Character %s (%s)!', $characterName, $characterUuid));

                        $this->entityManager->delete($matchSearch);
                    }
                    $this->entityManager->persist($arena);
                    $this->entityManager->run();
                } else {
                    file_put_contents('match-search.txt', '- Only found ' . $matchCount . ' characters searching for match.' . PHP_EOL, FILE_APPEND);
                    foreach ($chunk as $matchSearch) {
                        $this->sendToUser($matchSearch->getCharacter()->getUser(), 'Still searching!');
                    }
                }
            }

            // do match handling
            /** @var ArenaRepository $arenaRepository */
            $arenaRepository = $this->orm->getRepository(Arena::class);

            foreach ($arenaRepository->findActiveArenas(5) as $arena) {
                foreach ($arena->getCharacters() as $character) {
                    $character->setCurrentArena(null);
                    $this->sendToUser($character->getUser(), 'Fighting.');
                }
                $arena->setActive(false);
            }
            $this->entityManager->run();

            $worker->send("OK");

            // reset some stateful services
            $this->finalizer->finalize();
        }
    }

    private function sendToUser(User $user, string $message)
    {
        $this->broadcast->publish('channel.' . $user->getUuid(), $message);
    }
}
