<?php

namespace App\Combat;

class Enemy
{

    public function __construct(
        protected int $currentHealth
    ) {
    }

    public function setCurrentHealth(int $currentHealth): void
    {
        $this->currentHealth = $currentHealth;
    }

    public function getCurrentHealth(): int
    {
        return $this->currentHealth;
    }
}
