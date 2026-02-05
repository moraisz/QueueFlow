<?php

namespace Src\Contracts\Interfaces\Database;

use PDOStatement;

interface QueryBuilderInterface
{
    /**
     * @param array<int, string> $columns
     */
    public function select(array $columns): self;

    public function from(string $table): self;

    public function where(string $column, string $operator, mixed $value): self;

    public function join(string $table, string $first, string $operator, string $second): self;

    public function leftJoin(string $table, string $first, string $operator, string $second): self;

    public function orderBy(string $column, string $direction = 'ASC'): self;

    public function limit(int $limit): self;

    /**
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): PDOStatement|bool|array;

    /**
     * @param array<string, mixed> $data
     */
    public function update(array $data): PDOStatement|bool;

    public function delete(): PDOStatement|bool;

    /**
     * @param array<string, string> $columns
     */
    public function create(string $tableName, array $columns): PDOStatement|bool;

    public function drop(string $tableName): PDOStatement|bool;

    public function getSql(): string;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get(): array;
}
