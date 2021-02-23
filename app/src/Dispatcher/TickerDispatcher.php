<?php

declare(strict_types=1);

namespace App\Dispatcher;

use App\Combat\CombatHandler;
use App\Database\Arena;
use App\Database\MatchSearch;
use App\Database\User;
use App\Repository\ArenaRepository;
use App\Repository\MatchSearchRepository;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Psr\Log\LoggerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;

class TickerDispatcher implements DispatcherInterface
{

    const CHARACTERS_NEEDED_FOR_MATCH = 3;
    const TICKS_PER_MINUTE = 15;

    private ORMInterface $orm;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private EnvironmentInterface $env;
    private BroadcastInterface $broadcast;
    private FinalizerInterface $finalizer;

    private CombatHandler $combatHandler;

    public function __construct(
        ORMInterface $orm,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        EnvironmentInterface $env,
        BroadcastInterface $broadcast,
        FinalizerInterface $finalizer,
        CombatHandler $combatHandler
    ) {
        $this->orm = $orm;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->env = $env;
        $this->broadcast = $broadcast;
        $this->finalizer = $finalizer;
        $this->combatHandler = $combatHandler;
    }

    public function canServe(): bool
    {
        return php_sapi_name() === 'cli' && $this->env->get('RR_TICKER') !== null;
    }

    public function serve(): void
    {
        while (true) {
            /** @var ArenaRepository $arenaRepository */
            $arenaRepository = $this->orm->getRepository(Arena::class);

            /** @var MatchSearchRepository $matchSearchRepository */
            $matchSearchRepository = $this->orm->getRepository(MatchSearch::class);

            // do matchmaking
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

                        $this->sendToUser($character->getUser(), sprintf('Match %d found for Character %s (%s)!', $chunkIndex, $characterName, $characterUuid));

                        $this->entityManager->delete($matchSearch);
                    }
                    $this->entityManager->persist($arena);
                    $this->entityManager->run();
                } else {
                    $this->logger->info('Only found ' . $matchCount . ' characters searching for match.');
                    foreach ($chunk as $matchSearch) {
                        $this->sendToUser($matchSearch->getCharacter()->getUser(), 'Still searching!');
                    }
                }
            }

            // do match handling
            $activeArenas = $arenaRepository->findActiveArenas(5);
            foreach ($activeArenas as $arena) {
                $this->combatHandler->battle($arena);

                $this->logger->debug('Arena ' . $arena->getUuid() . ' is ' . $arena->isActive() ? 'active' : 'inactive');
                foreach ($arena->getCharacters() as $character) {
                    $character->setCurrentArena(null);
                    $this->sendToUser($character->getUser(), sprintf('Fighting in arena %s.', $arena->getUuid()));
                }
                $arena->setActive(false);
            }
            $this->entityManager->run();

            // reset some stateful services
            $this->finalizer->finalize();

            sleep(60 / static::TICKS_PER_MINUTE);
        }
    }

    private function sendToUser(User $user, string $message): void
    {
        $this->broadcast->publish('channel.' . $user->getUuid(), $message);
    }
}
