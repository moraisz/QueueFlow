<?php

namespace Src\Infrastructure\Database\Migrations;

use Src\Core\AbstractClasses\Migration;

return new class extends Migration {
    private string $tableName = 'customers';

    public function up(): void
    {
        $this->queryBuilder
            ->createTable($this->tableName, [
                'id' => 'SERIAL PRIMARY KEY',
                'name' => 'VARCHAR(100) NOT NULL',
                'email' => 'VARCHAR(100) UNIQUE NOT NULL',
                'telephone' => 'VARCHAR(15) UNIQUE',
                'priority' => 'VARCHAR(20) NOT NULL',
                'type' => 'VARCHAR(20) NOT NULL',
                'status' => 'VARCHAR(20) NOT NULL',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
            ])
            ->execute();
    }

    public function down(): void
    {
        $this->queryBuilder
            ->dropTable($this->tableName)
            ->execute();
    }
};
