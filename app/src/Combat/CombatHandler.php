<?php

namespace App\Combat;

use App\Broadcast\DeferredBroadcast;
use App\Database\Arena;
use App\Database\Character;
use Doctrine\Common\Collections\Collection;

class CombatHandler
{
    final public const DEFAULT_HEALTH = 100;

    /**
     * @var string[]
     */
    private array $combatLog = [];

    public function __construct(
        private readonly DeferredBroadcast $deferredBroadcast
    ) {
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
                $enemyDamage = random_int($currentLevel + 1, 3 * ($currentLevel + 1));
                $enemyEffectiveDamage = min($characterCurrentHealth, $enemyDamage);

                // apply damage
                $this->combatLog(sprintf('%s received %d damage.', $character->getName(), $enemyEffectiveDamage));
                $character->setCurrentHealth(max(0, $characterCurrentHealth - $enemyEffectiveDamage));

                if ($enemy->getCurrentHealth() <= 0) {
                    $this->combatLog(sprintf('%s perished.', $character->getName()));
                }
            }

            // character attack phase
            foreach ($arena->getCharacters() as $character) {
                if (($enemy = $this->select($enemies)) === null) {
                    break 2;
                }
                $enemyCurrentHealth = $enemy->getCurrentHealth();
                $characterDamage = random_int(5, 15);
                $characterEffectiveDamage = min($enemyCurrentHealth, $characterDamage);

                // apply damage
                $this->combatLog(sprintf('%s dealt %d damage.', $character->getName(), $characterEffectiveDamage));
                $enemy->setCurrentHealth($enemyCurrentHealth - $characterEffectiveDamage);

                if ($enemy->getCurrentHealth() <= 0) {
                    $this->combatLog('Enemy perished.');
                }
            }
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

        // state update
        $this->sendCombatLog($arena);
        foreach ($arena->getCharacters() as $character) {
            $this->sendCharacterState($character);
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
        $numberOfEnemies = random_int($currentLevel, $arena->getLevels());

        $enemies = [];
        for ($i = 0; $i < $numberOfEnemies; $i++) {
            // enemies get health equivalent to current level times 10
            $enemies[] = new Enemy($currentLevel * 10);
        }
        return $enemies;
    }

    private function sendCharacterState(Character $character): void
    {
        if ($character->getUser() !== null) {
            $this->deferredBroadcast->sendToUser($character->getUser(), 'character', $character);
        }
    }

    private function sendCombatLog(Arena $arena): void
    {
        foreach ($arena->getCharacters() as $character) {
            if ($character->getUser() !== null) {
                $this->deferredBroadcast->sendToUser($character->getUser(), 'combat_log', $this->combatLog);
            }
        }
        $this->combatLog = [];
    }

    private function combatLog(string $message): void
    {
        $this->combatLog[] = $message;
    }
}
