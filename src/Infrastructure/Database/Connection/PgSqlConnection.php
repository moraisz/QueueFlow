<?php

namespace Src\Infrastructure\Database\Connection;

use PDO;
use PDOException;
use PDOStatement;
use Src\Contracts\Interfaces\Database\DatabaseConnectionInterface;

class PgSqlConnection implements DatabaseConnectionInterface
{
    private ?PDO $connection = null;
    private bool $isConnected = false;

    public function connect(): void
    {
        if ($this->isConnected) {
            return;
        }

        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $database = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        if (!$host || !$port || !$database || !$username || $password === false) {
            throw new PDOException('Missing required database environment variables');
        }

        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $host,
                $port,
                $database,
            );

            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $this->isConnected = true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function disconnect(): void
    {
        if (!$this->isConnected) {
            return;
        }

        $this->connection = null;
        $this->isConnected = false;
    }

    public function getConnection(): mixed
    {
        return $this->connection;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function execute(string $query, array $bindings = []): PDOStatement|bool
    {
        $this->connect();
        $stmt = $this->connection->prepare($query);
        $stmt->execute($bindings);
        return $stmt;
    }
}
