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

    private TransactionInterface $tr;

    private EnvironmentInterface $env;

    private FinalizerInterface $finalizer;

    private ContainerInterface $container;

    public function __construct(
        TransactionInterface $tr,
        EnvironmentInterface $env,
        FinalizerInterface $finalizer,
        ContainerInterface $container
    ) {
        $this->tr = $tr;
        $this->env = $env;
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

        /** @var BroadcastInterface $broadcast */
        $broadcast = $this->container->get(BroadcastInterface::class);

        /** @var ORMInterface $orm */
        $orm = $this->container->get(ORMInterface::class);

        /** @var MatchSearchRepository $matchSearchRepository */
        $matchSearchRepository = $orm->getRepository(MatchSearch::class);

        while (($body = $worker->receive($ctx)) !== null) {
            $lastTick = json_decode($ctx)->lastTick;
            $numTick = json_decode($body)->tick;

            // do matchmaking
            file_put_contents('match-search.txt', 'MatchSearch ' . $numTick . PHP_EOL, FILE_APPEND);
            $matchSearches = $matchSearchRepository->findOldestMatchSearches();
            $matchCount = count($matchSearches);
            if ($matchCount >= static::CHARACTERS_NEEDED_FOR_MATCH) {
                foreach ($matchSearches as $matchSearch) {
                    $character = $matchSearch->getCharacter();

                    $characterName = $character->getName();
                    $characterUuid = $character->getUuid();

                    $userUuid = $character->getUser()->getUuid();
                    $broadcast->publish(new Message('channel.' . $userUuid, sprintf('Match found for Character %s (%s)!', $characterName, $characterUuid)));

                    $this->tr->delete($matchSearch);
                }
                $this->tr->run();
            } else {
                file_put_contents('match-search.txt', '- Only found ' . $matchCount . ' characters searching for match.' . PHP_EOL, FILE_APPEND);
                foreach ($matchSearches as $matchSearch) {
                    $userUuid = $matchSearch->getCharacter()->getUser()->getUuid();
                    $broadcast->publish(new Message('channel.' . $userUuid, 'Still searching!'));
                }
            }

            // do match handling

            $worker->send("OK");

            // reset some stateful services
            $this->finalizer->finalize();
        }
    }
}
