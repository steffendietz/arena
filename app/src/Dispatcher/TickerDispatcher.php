<?php

declare(strict_types=1);

namespace App\Dispatcher;

use App\Database\MatchSearch;
use App\Repository\MatchSearchRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\TransactionInterface;
use Psr\Container\ContainerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\Broadcast\BroadcastInterface;
use Spiral\Broadcast\Message;
use Spiral\RoadRunner\Worker;

class TickerDispatcher implements DispatcherInterface
{

    const CHARACTERS_NEEDED_FOR_MATCH = 3;

    private ORMInterface $orm;
    private TransactionInterface $tr;
    private EnvironmentInterface $env;
    private BroadcastInterface $broadcast;
    private FinalizerInterface $finalizer;
    private ContainerInterface $container;

    public function __construct(
        ORMInterface $orm,
        TransactionInterface $tr,
        EnvironmentInterface $env,
        BroadcastInterface $broadcast,
        FinalizerInterface $finalizer,
        ContainerInterface $container
    ) {
        $this->tr = $tr;
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
            $matchSearches = $matchSearchRepository->findOldestMatchSearches();
            foreach (array_chunk($matchSearches, static::CHARACTERS_NEEDED_FOR_MATCH) as $chunkIndex => $chunk) {
                $matchCount = count($chunk);
                if ($matchCount >= static::CHARACTERS_NEEDED_FOR_MATCH) {
                    foreach ($chunk as $matchSearch) {
                        $character = $matchSearch->getCharacter();

                        $characterName = $character->getName();
                        $characterUuid = $character->getUuid();

                        $userUuid = $character->getUser()->getUuid();
                        $this->broadcast->publish(new Message('channel.' . $userUuid, sprintf('Match ' . $chunkIndex . ' found for Character %s (%s)!', $characterName, $characterUuid)));

                        $this->tr->delete($matchSearch);
                    }
                    $this->tr->run();
                } else {
                    file_put_contents('match-search.txt', '- Only found ' . $matchCount . ' characters searching for match.' . PHP_EOL, FILE_APPEND);
                    foreach ($chunk as $matchSearch) {
                        $userUuid = $matchSearch->getCharacter()->getUser()->getUuid();
                        $this->broadcast->publish(new Message('channel.' . $userUuid, 'Still searching!'));
                    }
                }
            }

            // do match handling

            $worker->send("OK");

            // reset some stateful services
            $this->finalizer->finalize();
        }
    }
}
