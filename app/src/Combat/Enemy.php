<?php

namespace App\Combat;

class Enemy
{

    protected $currentHealth;

    public function __construct(int $initialHealth)
    {
        $this->currentHealth = $initialHealth;
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
