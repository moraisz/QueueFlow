<?php

namespace Bootstrap;

use Src\Contracts\Interfaces\Database\DatabaseConnectionInterface;
use Src\Core\Request;
use Src\Core\Response;
use Src\Core\Container;
use Src\Core\Migrator;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;
use Src\Infrastructure\Database\Connection\PgSqlConnection;
use Src\Infrastructure\Repositories\CustomerPgSqlRepository;
use Src\Core\Router;
use Src\Infrastructure\Routers\CustomerRouter;
use Src\Contracts\Interfaces\Database\SqlQueryBuilderInterface;
use Src\Infrastructure\Database\QueryBuilder\PgSqlQueryBuilder;

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
        $this->container->singleton(DatabaseConnectionInterface::class, PgSqlConnection::class);
        $this->container->singleton(CustomerRepositoryInterface::class, CustomerPgSqlRepository::class);

        $this->container->bind(SqlQueryBuilderInterface::class, PgSqlQueryBuilder::class);
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

    public function handleCommand(): int
    {
        global $argv;

        if (!isset($argv[1])) {
            echo "No command provided\n";
            return 1;
        }

        if ($argv[1] === 'migrate') {
            $container = new Container();
            $container->singleton(DatabaseConnectionInterface::class, PgSqlConnection::class);
            $container->bind(SqlQueryBuilderInterface::class, PgSqlQueryBuilder::class);
            $migrator = $container->make(Migrator::class);

            if (isset($argv[2]) && $argv[2] === 'run') {
                echo "Running migrations...\n";
                $migrator->run();
                return 0;
            }

            if (isset($argv[2]) && $argv[2] === 'rollback') {
                echo "Rolling back migrations...\n";
                $steps = (int)($argv[2] ?? 1);
                $migrator->rollback($steps);
                return 0;
            }

            echo "Unknown migrate command...\n";
            return 1;
        }

        echo "Unknown command: {$argv[1]}\n";
        return 1;
    }
}
