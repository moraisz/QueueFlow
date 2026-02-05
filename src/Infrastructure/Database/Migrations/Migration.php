<?php

namespace Src\Infrastructure\Database\Migrations;

use Src\Contracts\Interfaces\Database\QueryBuilderInterface;

abstract class Migration
{
    protected QueryBuilderInterface $queryBuilder;

    public function setQueryBuilder(QueryBuilderInterface $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    abstract public function up(): void;
    abstract public function down(): void;
}
