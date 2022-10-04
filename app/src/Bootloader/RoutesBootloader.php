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
use Spiral\Router\Target\Group;
use Spiral\Router\Target\Namespaced;

class RoutesBootloader extends Bootloader
{
    public function boot(RouterInterface $router): void
    {
        // named routes
        $router->setRoute('html', $this->homeRoute());
        $router->setRoute('characterGenerate', $this->characterGenerateRoute());

        // api
        $this->apiRoute($router, [
            'character' => CharacterController::class,
        ]);

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

    protected function apiRoute(RouterInterface $router, array $groupControllers): void
    {
        $apiList = new Route('/v1/<controller>', new Group($groupControllers, Controller::RESTFUL));
        $router->setRoute(
            'api.list',
            $apiList->withVerbs('GET')->withDefaults(['action' => 'list'])
        );

        $api = new Route('/v1/<controller>/<id>', new Group($groupControllers, Controller::RESTFUL));
        $router->setRoute(
            'api.get',
            $api->withVerbs('GET')->withDefaults(['action' => 'load'])
        );
        $router->setRoute(
            'api.store',
            $api->withVerbs('POST')->withDefaults(['action' => 'store'])
        );
        $router->setRoute(
            'api.delete',
            $api->withVerbs('DELETE')->withDefaults(['action' => 'delete'])
        );

        $apiAction = new Route('/v1/<controller>/<action>/<id>', new Group($groupControllers, Controller::RESTFUL));
        $router->setRoute(
            'api.action',
            $apiAction->withVerbs('POST')
        );
    }

    /**
     * Default route points to namespace of controllers.
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
            'action' => 'index'
        ]);
    }
}
