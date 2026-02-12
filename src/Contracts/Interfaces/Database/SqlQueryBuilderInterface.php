<?php

namespace Src\Contracts\Interfaces\Database;

interface SqlQueryBuilderInterface
{
    public function select(array $columns): self;

    public function from(string $table): self;

    public function where(string $column, string $operator, mixed $value): self;

    public function join(string $table, string $first, string $operator, string $second): self;

    public function leftJoin(string $table, string $first, string $operator, string $second): self;

    public function orderBy(string $column, string $direction = 'ASC'): self;

    public function limit(int $limit): self;

    public function insertInto(string $tableName, array $columns): self;

    /**
     * @param array<int,mixed> $data
     */
    public function values(array $data): self;

    public function update(string $tableName): self;

    /**
     * @param array<int,string> $data
     */
    public function set(array $data): self;

    public function deleteFrom(string $tableName): self;

    public function createTable(string $tableName, array $columns): self;

    public function dropTable(string $tableName): self;

    public function getQuery(): string;

    public function execute(): array;
}
