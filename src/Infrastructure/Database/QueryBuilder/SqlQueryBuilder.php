<?php

namespace Src\Infrastructure\Database\QueryBuilder;

use PDO;
use PDOStatement;
use Src\Contracts\Interfaces\Database\DatabaseConnectionInterface;
use Src\Contracts\Interfaces\Database\QueryBuilderInterface;

class SqlQueryBuilder implements QueryBuilderInterface
{
    private DatabaseConnectionInterface $dbConnection;
    private string $table = '';
    private array $select = [];
    private array $where = [];
    private array $bindings = [];
    private array $joins = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(DatabaseConnectionInterface $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    private function reset(): void
    {
        $this->table = '';
        $this->select = [];
        $this->where = [];
        $this->bindings = [];
        $this->joins = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
    }

    public function select(array $columns): self
    {
        $this->select = $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->where[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        // Se o operador é IN e o valor é array, adiciona cada elemento aos bindings
        if ($operator === 'IN' && is_array($value)) {
            $this->bindings = array_merge($this->bindings, $value);
        } else {
            $this->bindings[] = $value;
        }

        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function insert(string $table, array $data): PDOStatement|bool|array
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders}) RETURNING *";

        $stmt = $this->dbConnection->execute($sql, array_values($data));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->reset();
        return $result;
    }

    public function update(array $data): PDOStatement|bool
    {
        $set = [];
        $values = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $values[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);

        if (!empty($this->where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($this->where as $index => $condition) {
                $prefix = $index === 0 ? '' : $condition['type'] . ' ';
                $conditions[] = $prefix . "{$condition['column']} {$condition['operator']} ?";
                $values[] = $condition['value'];
            }
            $sql .= implode(' ', $conditions);
        }

        return $this->dbConnection->execute($sql, $values);
    }

    public function delete(): PDOStatement|bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($this->where as $index => $condition) {
                $prefix = $index === 0 ? '' : $condition['type'] . ' ';
                $conditions[] = $prefix . "{$condition['column']} {$condition['operator']} ?";
            }
            $sql .= implode(' ', $conditions);
        }

        return $this->dbConnection->execute($sql, $this->bindings);
    }

    public function create(string $tableName, array $columns): PDOStatement|bool
    {
        $cols = [];
        foreach ($columns as $name => $type) {
            $cols[] = "{$name} {$type}";
        }
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (" . implode(', ', $cols) . ")";
        return $this->dbConnection->execute($sql);
    }

    public function drop(string $tableName): PDOStatement|bool
    {
        $sql = "DROP TABLE IF EXISTS {$tableName}";
        return $this->dbConnection->execute($sql);
    }

    public function getSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->select);

        if ($this->table) {
            $sql .= " FROM {$this->table}";
        }

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($this->where as $index => $condition) {
                $prefix = $index === 0 ? '' : $condition['type'] . ' ';

                if ($condition['operator'] === 'IN' && is_array($condition['value'])) {
                    $placeholders = implode(', ', array_fill(0, count($condition['value']), '?'));
                    $conditions[] = $prefix . "{$condition['column']} {$condition['operator']} ({$placeholders})";
                } else {
                    $conditions[] = $prefix . "{$condition['column']} {$condition['operator']} ?";
                }
            }
            $sql .= implode(' ', $conditions);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $sql;
    }

    public function get(): array
    {
        $stmt = $this->dbConnection->execute($this->getSql(), $this->bindings);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->reset();
        return $result;
    }
}
