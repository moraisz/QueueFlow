<?php

namespace Src\Core\AbstractClasses;

use Src\Contracts\Interfaces\Database\SqlQueryBuilderInterface;

abstract class Migration
{
    protected SqlQueryBuilderInterface $queryBuilder;

    public function setQueryBuilder(SqlQueryBuilderInterface $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    abstract public function up(): void;
    abstract public function down(): void;
}
