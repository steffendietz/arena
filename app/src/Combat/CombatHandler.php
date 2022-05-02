<?php

namespace App\Combat;

use App\Database\Arena;
use App\Database\Character;
use Doctrine\Common\Collections\Collection;
use Spiral\RoadRunner\Broadcast\BroadcastInterface;

class CombatHandler
{

    const DEFAULT_HEALTH = 100;

    private BroadcastInterface $broadcast;

    public function __construct(BroadcastInterface $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function bootstrap(Arena $arena): void
    {
        foreach ($arena->getCharacters() as $character) {
            $character->setCurrentHealth(self::DEFAULT_HEALTH);
        }
    }

    public function battle(Arena $arena): void
    {
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
                // BUG currently always deal 0 damage in first level
                $enemyDamage = rand(1 * $currentLevel, 3 * $currentLevel);
                $enemyEffectiveDamage = min($characterCurrentHealth, $enemyDamage);

                // apply damage
                $message = sprintf('%s received %d damage.', $character->getName(), $enemyEffectiveDamage);
                $this->sendArenaMessage($arena, $message);
                $character->setCurrentHealth(max(0, $characterCurrentHealth - $enemyEffectiveDamage));

                if ($enemy->getCurrentHealth() <= 0) {
                    $message = sprintf('%s perished.', $character->getName());
                    $this->sendArenaMessage($arena, $message);
                }
            }

            // character attack phase
            foreach ($arena->getCharacters() as $character) {
                if (($enemy = $this->select($enemies)) === null) {
                    break 2;
                }
                $enemyCurrentHealth = $enemy->getCurrentHealth();
                $characterDamage = rand(5, 15);
                $characterEffectiveDamage = min($enemyCurrentHealth, $characterDamage);

                // apply damage
                $message = sprintf('%s dealt %d damage.', $character->getName(), $characterEffectiveDamage);
                $this->sendArenaMessage($arena, $message);
                $enemy->setCurrentHealth($enemyCurrentHealth - $characterEffectiveDamage);

                if ($enemy->getCurrentHealth() <= 0) {
                    $this->sendArenaMessage($arena, 'Enemy perished.');
                }
            }

            break 1;
        }

        $currentLevel++;
        if ($currentLevel > $arena->getLevels()) {
            foreach ($arena->getCharacters() as $character) {
                $character->setCurrentArena(null);
            }
            $arena->setActive(false);
            $this->sendArenaMessage($arena, sprintf('Arena %s is finished!', $arena->getUuid()));
        } else {
            $arena->setCurrentLevel($currentLevel);
        }
    }

    /**
     * @return Character|Enemy|null
     */
    private function select(Collection|array $selectables)
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

    /**
     * @param Enemy[] $enemies
     */
    private function getEnemyHealth(array $enemies): int
    {
        $enemyHealth = 0;
        foreach ($enemies as $enemy) {
            $enemyHealth += $enemy->getCurrentHealth();
        }
        return $enemyHealth;
    }

    /**
     * @return Enemy[]
     */
    private function spawnEnemies(Arena $arena): array
    {
        $currentLevel = $arena->getCurrentLevel() + 1;
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
        $this->broadcast->publish('channel.' . $character->getUser()->getUuid(), $message);
    }

    private function sendArenaMessage(Arena $arena, string $message): void
    {
        foreach ($arena->getCharacters() as $character) {
            if ($character->getUser() !== null) {
                $this->sendCharacterMessage($character, $message);
            }
        }
    }
}
