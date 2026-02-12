<?php

namespace Src\Infrastructure\Repositories;

use Src\Application\Entities\CustomerEntity;
use Src\Contracts\Interfaces\Database\SqlQueryBuilderInterface;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class CustomerPgSqlRepository implements CustomerRepositoryInterface
{
    private SqlQueryBuilderInterface $queryBuilder;

    public function __construct(SqlQueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getAll(): array
    {
        $query = $this->queryBuilder
            ->select(['*'])
            ->from('customers')
            ->where(
                'status',
                'NOT IN',
                ['active']
            )
            ->execute();

        $customers = [];
        foreach ($query as $row) {
            $customers[] = new CustomerEntity(
                $row['name'],
                $row['priority'],
                $row['type'],
                $row['status'],
                $row['email'],
                $row['telephone'],
                (int)$row['id']
            );
        }

        return $customers;
    }

    public function getById(int $id): ?CustomerEntity
    {
        $query = $this->queryBuilder
            ->select(['*'])
            ->from('customers')
            ->where('id', '=', $id)
            ->execute();

        $customer = [];
        foreach ($query as $row) {
            $customer[] = new CustomerEntity(
                $row['name'],
                $row['priority'],
                $row['type'],
                $row['status'],
                $row['email'],
                $row['telephone'],
                (int)$row['id']
            );
        }

        return $customer[0] ?? null;
    }

    public function save(CustomerEntity $customer): CustomerEntity
    {
        $query = $this->queryBuilder
            ->insertInto('customers', [
                'name',
                'priority',
                'type',
                'status',
                'email',
                'telephone'
            ])
            ->values([
                $customer->getName(),
                $customer->getPriority(),
                $customer->getType(),
                $customer->getStatus(),
                $customer->getEmail(),
                $customer->getTelephone()
            ])
            ->execute();

        $customer = [];
        foreach ($query as $row) {
            $customer[] = new CustomerEntity(
                $row['name'],
                $row['priority'],
                $row['type'],
                $row['status'],
                $row['email'],
                $row['telephone'],
                (int)$row['id']
            );
        }

        return $customer[0];
    }
}
