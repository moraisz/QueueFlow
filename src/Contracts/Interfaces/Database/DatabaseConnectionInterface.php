<?php

namespace Src\Contracts\Interfaces\Database;

interface DatabaseConnectionInterface
{
    public function connect(): void;
    public function disconnect(): void;
    public function getConnection(): mixed;
    public function beginTransaction(): void;
    public function isConnected(): bool;
    public function commit(): void;
    public function rollback(): void;
    public function execute(string $query, array $bindings): mixed;
}
