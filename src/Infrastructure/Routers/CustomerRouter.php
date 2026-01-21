<?php

namespace Src\Infrastructure\Routers;

use Src\Infrastructure\Http\Router;
use Src\Infrastructure\Controllers\CustomerController;

class CustomerRouter {
    public static function register(Router $router): void {
        $router->get('/customers', [CustomerController::class, 'get']);
        $router->get('/customers/{id}', [CustomerController::class, 'getUnique']);
        $router->post('/customers', [CustomerController::class, 'post']);
        $router->put('/customers/{id}', [CustomerController::class, 'put']);
        $router->delete('/customers/{id}', [CustomerController::class, 'delete']);
    }
}
