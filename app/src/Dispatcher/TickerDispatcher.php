<?php

declare(strict_types=1);

namespace App\Dispatcher;

use Psr\Container\ContainerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\RoadRunner\Worker;

class TickerDispatcher implements DispatcherInterface
{

    private EnvironmentInterface $env;

    private FinalizerInterface $finalizer;

    private ContainerInterface $container;

    public function __construct(
        EnvironmentInterface $env,
        FinalizerInterface $finalizer,
        ContainerInterface $container
    ) {
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

        while (($body = $worker->receive($ctx)) !== null) {
            $lastTick = json_decode($ctx)->lastTick;
            $numTick = json_decode($body)->tick;

            // do something
            file_put_contents(
                'ticks.txt',
                json_encode([
                    'numTick' => $numTick,
                    'lastTick' => $lastTick,
                ]) . PHP_EOL,
                FILE_APPEND
            );
            // simulate long running task
            sleep(10);

            $worker->send("OK");

            // reset some stateful services
            $this->finalizer->finalize();
        }
    }
}
