<?php

namespace Src\Infrastructure\Database\QueryBuilder;

use PDO;
use Src\Contracts\Interfaces\Database\DatabaseConnectionInterface;
use Src\Contracts\Interfaces\Database\SqlQueryBuilderInterface;

class PgSqlQueryBuilder implements SqlQueryBuilderInterface
{
    private DatabaseConnectionInterface $dbConnection;

    private array $select = [];
    private string $from = '';
    private string $insertTable = '';
    private array $insertColumns = [];
    private array $insertValues = [];
    private string $updateTable = '';
    private array $updateData = [];
    private string $createTable = '';
    private array $createColumns = [];
    private string $deleteFrom = '';
    private string $dropTable = '';
    private array $where = [];
    private array $bindings = [];
    private array $joins = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    private const VALID_OPERATORS = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN', 'NOT IN'];

    public function __construct(DatabaseConnectionInterface $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    private function reset(): void
    {
        $this->select = [];
        $this->from = '';
        $this->insertTable = '';
        $this->insertColumns = [];
        $this->insertValues = [];
        $this->updateTable = '';
        $this->updateData = [];
        $this->createTable = '';
        $this->deleteFrom = '';
        $this->dropTable = '';
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
        $this->from = $table;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        if (!in_array($operator, self::VALID_OPERATORS)) {
            throw new \InvalidArgumentException("Invalid operator: {$operator}");
        }

        $this->where[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        if (($operator === 'IN' || $operator === 'NOT IN') && is_array($value)) {
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

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function insertInto(string $tableName, array $columns): self
    {
        $this->insertTable = $tableName;
        $this->insertColumns = $columns;
        return $this;
    }

    /**
     * @param array<int,mixed> $data
     */
    public function values(array $values): self
    {
        $this->insertValues[] = $values;
        foreach ($values as $value) {
            $this->bindings[] = $value;
        }
        return $this;
    }

    public function update(string $tableName): self
    {
        $this->updateTable = $tableName;
        return $this;
    }

    /**
     * @param array<int,mixed> $data
     */
    public function set(array $data): self
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $this->bindings[] = $value;
        }

        $this->updateData = $set;
        return $this;
    }

    public function deleteFrom(string $tableName): self
    {
        $this->deleteFrom = $tableName;
        return $this;
    }

    public function createTable(string $tableName, array $columns): self
    {
        $this->createTable = $tableName;
        $this->createColumns = $columns;
        return $this;
    }

    public function dropTable(string $tableName): self
    {
        $this->dropTable = $tableName;
        return $this;
    }

    public function getQuery(): string
    {
        $sql = '';

        if (!empty($this->select)) {
            $sql .= "SELECT " . implode(', ', $this->select);
        }

        if ($this->from) {
            $sql .= " FROM {$this->from}";
        }

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if ($this->updateTable) {
            $sql .= "UPDATE {$this->updateTable} SET ";
        }

        if ($this->updateData) {
            $sql .= implode(', ', $this->updateData);
        }

        if ($this->deleteFrom) {
            $sql .= "DELETE FROM {$this->deleteFrom}";
        }

        if (!empty($this->where)) {
            $sql .= " WHERE ";

            $conditions = [];
            foreach ($this->where as $index => $condition) {
                $prefix = $index === 0 ? '' : $condition['type'] . ' ';

                if (
                    ($condition['operator'] === 'IN' || $condition['operator'] === 'NOT IN')
                        && is_array($condition['value'])
                ) {
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

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        if ($this->insertTable && !empty($this->insertColumns)) {
            $columns = implode(', ', $this->insertColumns);
            $sql .= "INSERT INTO {$this->insertTable} ({$columns}) VALUES";

            $valueGroups = [];
            foreach ($this->insertValues as $values) {
                $placeholders = implode(', ', array_fill(0, count($values), '?'));
                $valueGroups[] = "({$placeholders})";
            }
            $sql .= ' ' . implode(', ', $valueGroups);
        }

        if ($this->createTable && !empty($this->createColumns)) {
            $cols = [];
            foreach ($this->createColumns as $name => $type) {
                $cols[] = "{$name} {$type}";
            }

            $sql .= "CREATE TABLE IF NOT EXISTS {$this->createTable} (" . implode(', ', $cols) . ")";
        }

        if ($this->dropTable) {
            $sql .= "DROP TABLE IF EXISTS {$this->dropTable}";
        }

        return $sql;
    }

    public function execute(): array
    {
        $stmt = $this->dbConnection->execute($this->getQuery(), $this->bindings);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->reset();
        return $result;
    }
}
