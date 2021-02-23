<?php

namespace App\Combat;

use App\Database\Arena;
use App\Database\Character;
use Spiral\Broadcast\BroadcastInterface;
use Spiral\Broadcast\Message;

class CombatHandler
{

    private BroadcastInterface $broadcast;

    public function __construct(BroadcastInterface $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function battle(Arena $arena)
    {
        $this->sendArenaMessage($arena, 'battling');
        return;
        $currentLevel = $arena->getCurrentLevel();

        // spawn enemies
        $enemies = $this->spawnEnemies($arena);

        while ($this->getCharacterHealth($arena) > 0 && $this->getEnemyHealth($enemies) > 0) {


            // enemy attack phase
            foreach ($enemies as $enemy) {
                if (($character = $this->select($arena->getCharacters())) === null) {
                    break 2;
                }
                $characterCurrentHealth = $character->getCurrentHealth();
                $enemyDamage = rand(1 * $currentLevel, 3 * $currentLevel);
                $enemyEffectiveDamage = min($characterCurrentHealth, $enemyDamage);

                // apply damage
                $message = sprintf('%s received %d damage.', $character->getName(), $enemyEffectiveDamage);
                $this->sendCharacterMessage($character, $message);
                $character->setCurrentHealth($characterCurrentHealth - $enemyEffectiveDamage);
            }

            // character attack phase
            foreach ($arena->getCharacters() as $character) {
                if (($enemy = $this->select($enemies)) === null) {
                    break 2;
                }
                $enemyCurrentHealth = $enemy;
                $characterDamage = rand(5, 15);
                $characterEffectiveDamage = min($enemyCurrentHealth, $characterDamage);

                // apply damage
                $message = sprintf('%s dealt %d damage.', $character->getName(), $characterEffectiveDamage);
                $this->sendCharacterMessage($character, $message);
                $enemy->setCurrentHealth($enemyCurrentHealth - $characterEffectiveDamage);
            }

            break 1;
        }

        $currentLevel++;
        if ($currentLevel > $arena->getLevels()) {
            foreach ($arena->getCharacters() as $character) {
                $character->setCurrentArena(null);
            }
            $arena->setActive(false);
        } else {
            $arena->setCurrentLevel($currentLevel);
        }
    }

    /**
     * @return Character|null
     */
    private function select(array $selectables)
    {
        $eligible = [];
        foreach ($selectables as $selectable) {
            if ($selectable->getCurrentHealth() > 0) {
                $eligible[] = $selectable;
            }
        }
        if (!empty($eligible)) {
            return $eligible[array_rand($eligible)];
        }
        return null;
    }

    private function getCharacterHealth(Arena $arena): int
    {
        $characterHealth = 0;
        foreach ($arena->getCharacters() as $character) {
            $characterHealth += $character->getCurrentHealth();
        }
        return $characterHealth;
    }

    private function getEnemyHealth($enemies): int
    {
        $enemyHealth = 0;
        foreach ($enemies as $enemy) {
            $enemyHealth += $enemy;
        }
        return $enemyHealth;
    }

    /**
     * @return Enemy[]
     */
    private function spawnEnemies(Arena $arena): array
    {
        $currentLevel = $arena->getCurrentLevel();
        $numberOfEnemies = rand($currentLevel, $arena->getLevels());

        $enemies = [];
        for ($i = 0; $i < $numberOfEnemies; $i++) {
            // enemies get health equivalent to current level times 10
            $enemies[] = new Enemy($currentLevel * 10);
        }
        return $enemies;
    }

    private function sendCharacterMessage(Character $character, string $message): void
    {
        $uuid = $character->getUser()->getUuid();
        $this->broadcast->publish(new Message(
            'channel.' . $uuid,
            $message
        ));
    }

    private function sendArenaMessage(Arena $arena, string $message): void
    {
        foreach ($arena->getCharacters() as $character) {
            $this->sendCharacterMessage($character, $message);
        }
    }
}
