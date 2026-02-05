<?php

namespace Bootstrap;

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Core\Container;
use Src\Core\Migrator;
use Src\Contracts\Interfaces\Database\DatabaseConnectionInterface;
use Src\Infrastructure\Database\Connection\PgSqlConnection;
use Src\Contracts\Interfaces\Database\QueryBuilderInterface;
use Src\Infrastructure\Database\QueryBuilder\SqlQueryBuilder;

$container = new Container();
$container->singleton(DatabaseConnectionInterface::class, PgSqlConnection::class);
$container->bind(QueryBuilderInterface::class, SqlQueryBuilder::class);

$migrator = $container->make(Migrator::class);

if (($argv[1] ?? '') === 'rollback') {
    $steps = (int)($argv[2] ?? 1);
    $migrator->rollback($steps);
} else {
    $migrator->run();
}
