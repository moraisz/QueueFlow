<?php

namespace Src\Core;

use Src\Contracts\Interfaces\Database\SqlQueryBuilderInterface;
use Src\Infrastructure\Database\Migrations\Migration;

class Migrator
{
    private SqlQueryBuilderInterface $queryBuilder;
    private string $migrationsPath;

    public function __construct(SqlQueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->migrationsPath = __DIR__ . '/../Infrastructure/Database/Migrations';
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->queryBuilder
            ->createTable('migrations', [
                'id' => 'SERIAL PRIMARY KEY',
                'migration' => 'VARCHAR(255) NOT NULL',
                'batch' => 'INTEGER NOT NULL',
                'executed_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
            ])
            ->execute();
    }

    public function run(): void
    {
        echo "Starting migrations...\n";
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        $executed = $this->getExecutedMigrations();
        $batch = $this->getNextBatch();

        foreach ($files as $file) {
            $migration = basename($file, '.php');

            if (in_array($migration, $executed) || $migration === 'Migration') {
                continue;
            }

            echo "Migrating: {$migration}\n";

            $instance = require $file;
            $instance->setQueryBuilder($this->queryBuilder);

            if (!$instance instanceof Migration) {
                throw new \Exception("Migration {$migration} must return a Migration instance");
            }

            $instance->up();

            $this->logMigration($migration, $batch);
            echo "Migrated: {$migration}\n";
        }
    }

    public function rollback(int $steps = 1): void
    {
        echo "Rolling back migrations...\n";

        $batches = $this->queryBuilder
            ->select(['DISTINCT batch'])
            ->from('migrations')
            ->orderBy('batch', 'DESC')
            ->limit($steps)
            ->execute();

        if (empty($batches)) {
            echo "Nothing to rollback\n";
            return;
        }

        $migrations = $this->queryBuilder
            ->select(['migration'])
            ->from('migrations')
            ->where('batch', 'IN', array_column($batches, 'batch'))
            ->orderBy('id', 'DESC')
            ->execute();

        foreach ($migrations as $migration) {
            $file = $this->migrationsPath . '/' . $migration['migration'] . '.php';

            echo "Rolling back: {$migration['migration']}\n";

            $instance = require $file;
            $instance->setQueryBuilder($this->queryBuilder);

            if (!$instance instanceof Migration) {
                throw new \Exception("Migration must return a Migration instance");
            }

            $instance->down();

            $this->removeMigration($migration['migration']);
            echo "Rolled back: {$migration['migration']}\n";
        }
    }

    private function getExecutedMigrations(): array
    {
        $result = $this->queryBuilder
            ->select(['migration'])
            ->from('migrations')
            ->execute();

        return array_column($result, 'migration');
    }

    private function getNextBatch(): int
    {
        $result = $this->queryBuilder
            ->select(['MAX(batch) as batch'])
            ->from('migrations')
            ->execute();

        return ($result['batch'] ?? 0) + 1;
    }

    private function logMigration(string $migration, int $batch): void
    {
        $this->queryBuilder
            ->insertInto('migrations', [
                'migration',
                'batch'
            ])
            ->values([
                $migration,
                $batch
            ])
            ->execute();
    }

    private function removeMigration(string $migration): void
    {
        $this->queryBuilder
            ->deleteFrom('migrations')
            ->where('migration', '=', $migration)
            ->execute();
    }
}
