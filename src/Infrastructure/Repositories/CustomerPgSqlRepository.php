<?php

namespace Src\Infrastructure\Repositories;

use Src\Application\Entities\CustomerEntity;
use Src\Contracts\Interfaces\Database\QueryBuilderInterface;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class CustomerPgSqlRepository implements CustomerRepositoryInterface
{
    private QueryBuilderInterface $queryBuilder;

    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getAll(): array
    {
        $query = $this->queryBuilder
            ->select(['*'])
            ->from('customers')
            ->get();

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
            ->get();

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
            ->insert('customers', [
                'name' => $customer->getName(),
                'priority' => $customer->getPriority(),
                'type' => $customer->getType(),
                'status' => $customer->getStatus(),
                'email' => $customer->getEmail(),
                'telephone' => $customer->getTelephone(),
            ]);

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
