<?php

namespace Src\Infrastructure\Database\Migrations;

return new class extends Migration {
    public function up(): void
    {
        $this->queryBuilder->create('customers', [
            'id' => 'SERIAL PRIMARY KEY',
            'name' => 'VARCHAR(100) NOT NULL',
            'email' => 'VARCHAR(100) UNIQUE NOT NULL',
            'telephone' => 'VARCHAR(15) UNIQUE',
            'priority' => 'VARCHAR(20) NOT NULL',
            'type' => 'VARCHAR(20) NOT NULL',
            'status' => 'VARCHAR(20) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);
    }

    public function down(): void
    {
        $this->queryBuilder->drop('customers');
    }
};
