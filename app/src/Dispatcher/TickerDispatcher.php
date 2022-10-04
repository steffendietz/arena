<?php

declare(strict_types=1);

namespace App\Dispatcher;

use App\Broadcast\DeferredBroadcast;
use App\Combat\CombatHandler;
use App\Database\Arena;
use App\Database\Character;
use App\Database\MatchSearch;
use App\Repository\ArenaRepository;
use App\Repository\MatchSearchRepository;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;

class TickerDispatcher implements DispatcherInterface
{

    final const CHARACTERS_NEEDED_FOR_MATCH = 3;
    final const TICKS_PER_MINUTE = 15;

    public function __construct(
        private readonly ORMInterface $orm,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly EnvironmentInterface $env,
        private readonly DeferredBroadcast $deferredBroadcast,
        private readonly FinalizerInterface $finalizer,
        private readonly CombatHandler $combatHandler
    ) {
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
            /** @var MatchSearch[] $chunk */
            foreach (array_chunk($matchSearches, static::CHARACTERS_NEEDED_FOR_MATCH) as $chunk) {
                $matchSearchCount = count($chunk);
                if ($matchSearchCount === static::CHARACTERS_NEEDED_FOR_MATCH) {
                    // create regular match
                    $this->createMatch($chunk);
                } elseif ($matchSearchCount > 0 && ($oldestMatchSearch = reset($chunk)) instanceof MatchSearch) {
                    $currentDateTime = new DateTimeImmutable();
                    $interval = $currentDateTime->diff($oldestMatchSearch->getStarted(), true);
                    if ($interval->s > 30) {
                        // create match with AI
                        $this->createMatch($chunk);
                    } else {
                        $this->logger->info(sprintf('Only found %d characters searching for match.', count($chunk)));
                        foreach ($chunk as $matchSearch) {
                            $this->deferredBroadcast->sendToUser(
                                $matchSearch->getCharacter()->getUser(),
                                'general',
                                'Still searching!'
                            );
                        }
                    }
                }
            }
            $this->entityManager->run();

            // do match handling
            $activeArenas = $arenaRepository->findActiveArenas(5);
            foreach ($activeArenas as $arena) {
                $this->combatHandler->battle($arena);
                $this->entityManager->persistState($arena);
                $this->entityManager->run();
            }

            // reset some stateful services
            $this->finalizer->finalize();

            sleep(60 / static::TICKS_PER_MINUTE);
        }
    }

    /**
     * @param MatchSearch[] $matchSearches
     */
    private function createMatch(array $matchSearches): void
    {
        $arena = new Arena();
        // fill with AI characters
        for ($i = 0; $i < self::CHARACTERS_NEEDED_FOR_MATCH - count($matchSearches); $i++) {
            $aiCharacter = new Character();
            $aiCharacter->setName('AiCharacter' . $i);
            $this->entityManager->persist($aiCharacter);
            $arena->addCharacter($aiCharacter);
        }
        foreach ($matchSearches as $matchSearch) {
            $character = $matchSearch->getCharacter();
            $character->setMatchSearch(null);

            // add character to match
            $arena->addCharacter($character);

            $this->entityManager->delete($matchSearch);

            if ($character->getUser()) {
                $this->deferredBroadcast->sendToUser(
                    $character->getUser(),
                    'character',
                    $character
                );
            }
        }
        $this->combatHandler->bootstrap($arena);
        $this->entityManager->persist($arena);
    }
}
