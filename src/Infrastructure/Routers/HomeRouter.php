<?php

namespace Src\Infrastructure\Routers;

use Src\Core\Router;
use Src\Infrastructure\Controllers\HomeController;

class HomeRouter
{
    public static function register(Router $router): void
    {
        $router->get('/', [HomeController::class, 'get']);
    }
}
