<?php

namespace App\Broadcast;

use App\Database\User;
use App\Interfaces\IdentifiableInterface;
use JsonSerializable;
use Spiral\Broadcasting\BroadcastInterface;

class DeferredBroadcast
{
    private array $deferredMessages = [];

    public function __construct(
        private readonly BroadcastInterface $broadcast
    ) {
    }

    public function sendToUser(User $user, string $namespace, JsonSerializable|array|string $payload): void
    {
        $this->broadcast->publish(
            'channel.' . $user->getUuid(),
            json_encode([$namespace => $payload], JSON_THROW_ON_ERROR)
        );
    }

    public function sendToUserDeferred(User $user, string $namespace, JsonSerializable|array|string $payload): void
    {
        if ($payload instanceof IdentifiableInterface) {
            $this->deferredMessages[$user->getUuid()][$namespace][$payload->getIdentifier()] = $payload;
        } elseif (is_array($payload)) {
            if (isset($this->deferredMessages[$user->getUuid()][$namespace])) {
                $this->deferredMessages[$user->getUuid()][$namespace] = array_merge(
                    $this->deferredMessages[$user->getUuid()][$namespace],
                    $payload
                );
            } else {
                $this->deferredMessages[$user->getUuid()][$namespace] = $payload;
            }
        } else {
            $this->deferredMessages[$user->getUuid()][$namespace][] = $payload;
        }
    }

    public function sendDeferredMessages(): void
    {
        foreach ($this->deferredMessages as $userUuid => $payload) {
            $this->broadcast->publish(
                'channel.' . $userUuid,
                json_encode($payload, JSON_THROW_ON_ERROR)
            );
        }
        $this->deferredMessages = [];
    }
}
