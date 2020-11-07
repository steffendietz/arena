<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Bootloader;

use App\Controller\CharacterController;
use App\Controller\HomeController;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Router\Route;
use Spiral\Router\RouteInterface;
use Spiral\Router\RouterInterface;
use Spiral\Router\Target\Controller;
use Spiral\Router\Target\Namespaced;

class RoutesBootloader extends Bootloader
{
    /**
     * Bootloader execute method.
     *
     * @param RouterInterface $router
     */
    public function boot(RouterInterface $router): void
    {
        // named routes
        $router->setRoute('html', $this->homeRoute());
        $router->setRoute('character', $this->characterGenerateRoute());
        $router->setRoute('searchmatch', $this->characterSearchMathRoute());

        // fallback (default) route
        $router->setDefault($this->defaultRoute());
    }

    protected function homeRoute(): RouteInterface
    {
        return new Route(
            '/<action>.html',
            new Controller(HomeController::class)
        );
    }

    protected function characterSearchMathRoute(): RouteInterface
    {
        $characterGenerateRoute = new Route(
            '/character/search-match/<characterUuid>',
            new Controller(CharacterController::class)
        );

        return $characterGenerateRoute->withDefaults([
            'action' => 'toggleMatchSearch'
        ]);
    }

    protected function characterGenerateRoute(): RouteInterface
    {
        $characterGenerateRoute = new Route(
            '/character/generate[/<name>]',
            new Controller(CharacterController::class)
        );

        return $characterGenerateRoute->withDefaults([
            'action' => 'generate',
            'name' => 'HelloWorldCharacter'
        ]);
    }

    /**
     * Default route points to namespace of controllers.
     *
     * @return RouteInterface
     */
    protected function defaultRoute(): RouteInterface
    {
        // handle all /controller/action like urls
        $route = new Route(
            '/[<controller>[/<action>]]',
            new Namespaced('App\\Controller')
        );

        return $route->withDefaults([
            'controller' => 'home',
            'action'     => 'index'
        ]);
    }
}
