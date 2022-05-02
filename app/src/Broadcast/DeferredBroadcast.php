<?php

namespace App\Broadcast;

use App\Database\User;
use JsonSerializable;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;

class DeferredBroadcast
{
    private BroadcastInterface $broadcast;
    private array $deferredMessages = [];

    public function __construct(BroadcastInterface $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function sendToUser(User $user, string $namespace, JsonSerializable|string $payload): void
    {
        $this->broadcast->publish('channel.' . $user->getUuid(), json_encode([
            $namespace => $payload,
        ]));
    }

    public function sendToUserDeferred(User $user, string $namespace, JsonSerializable|string $payload): void
    {
        if ($payload instanceof JsonSerializable) {
            $payload = json_encode($payload);
        }
        $this->deferredMessages[$user->getUuid()][$namespace][] = json_decode($payload, true);
    }

    public function sendDeferredMessages(): void
    {
        foreach ($this->deferredMessages as $userUuid => $payload) {
            $this->broadcast->publish('channel.' . $userUuid, json_encode($payload));
        }
        $this->deferredMessages = [];
    }
}
