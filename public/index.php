<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Infrastructure\Http\Request;
use Src\Infrastructure\Http\Response;
use Src\Infrastructure\Http\Router;
use Src\Infrastructure\Routers\CustomerRouter;

// configure to continue processing even if the client disconnects
ignore_user_abort(true);

// Routers registration
$router = new Router();
CustomerRouter::register($router);

// handler function for FrankenPHP
$handler = static function () use ($router) {
    try {
        // get all request data
        $request = Request::createFromGlobals();

        // dispatch and get response
        $response = $router->run($request);

        // send response
        $response->send();
        
    } catch (\Throwable $e) {
        // error handling
        $errorResponse = new Response();
        $errorResponse->json([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
        
        $errorResponse->send();
    }
};

if (getenv('FRANKENPHP_MODE') === 'worker') {
    // main loop to handle requests with FrankenPHP Worker mode
    $maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 1000);
    for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
        $keepRunning = \frankenphp_handle_request($handler);

        // perform garbage collection
        gc_collect_cycles();

        if (!$keepRunning) break;
    }
} else {
    $handler();
}
