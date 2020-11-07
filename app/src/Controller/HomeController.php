<?php

declare(strict_types=1);

namespace App\Controller;

use Spiral\Prototype\Traits\PrototypeTrait;

class HomeController
{
    use PrototypeTrait;

    /**
     * @return string
     */
    public function index(): string
    {
        return $this->views->render('home.dark.php');
    }
}
