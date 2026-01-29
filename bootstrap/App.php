<?php

namespace Bootstrap;

use Src\Core\Request;
use Src\Core\Response;
use Src\Core\Container;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;
use Src\Infrastructure\Repositories\CustomerMockRepository;
use Src\Core\Router;
use Src\Infrastructure\Routers\CustomerRouter;

class App
{
    private Router $router;
    private Container $container;

    public function run(): void
    {
        $this->container = new Container();
        $this->configureContainer();

        $this->router = new Router($this->container);
        $this->configureRouter();

        if ($this->isFrankenPhpWorkerMode()) {
            $this->runWorkerMode();
        } else {
            $this->handleRequest();
        }
    }

    private function isFrankenPhpWorkerMode(): bool
    {
        return getenv('FRANKENPHP_MODE') === 'worker';
    }

    private function configureContainer(): void
    {
        $this->container->singleton(CustomerRepositoryInterface::class, CustomerMockRepository::class);
    }

    private function configureRouter(): void
    {
        CustomerRouter::register($this->router);
    }

    private function runWorkerMode(): void
    {
        $maxRequests = (int) (getenv('MAX_REQUESTS') ?? 1000);

        // main loop to handle requests with FrankenPHP Worker mode
        for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
            $keepRunning = \frankenphp_handle_request(function () {
                $this->handleRequest();
            });

            // perform garbage collection
            gc_collect_cycles();

            if (!$keepRunning) {
                break;
            }
        }
    }

    // handler function for FrankenPHP
    private function handleRequest(): void
    {
        try {
            // get all request data
            $request = Request::createFromGlobals();

            // dispatch and get response
            $response = $this->router->run($request);

            // send response
            $response->send();
        } catch (\Throwable $e) {
            // error handling
            $errorResponse = new Response();
            $errorResponse->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);

            $errorResponse->send();
        }
    }
}
