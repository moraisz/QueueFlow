<?php

namespace Src\Infrastructure\Repositories;

use Src\Application\Entities\CustomerEntity;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class MockCustomerRepository implements CustomerRepositoryInterface {
    private array $customers;

    public function __construct()
    {
        $this->customers = [
            new CustomerEntity('João', 'normal', 'consulta', 'atendido', 'joao@email.com', '86999887766', 1),
            new CustomerEntity('Maria', 'urgente', 'exame', 'fila de espera', 'maria@email.com', '85999887766', 2),
            new CustomerEntity('Pedro', 'normal', 'tratamento', 'atendido', 'pedro@email.com', '84999887766', 3),
        ];
    }

    public function getAll(): array
    {
        return $this->customers;
    }

    public function getById(int $id): ?CustomerEntity
    {
        foreach ($this->customers as $customerData) {
            if ($customerData->getId() === $id) {
                return $customerData;
            }
        }

        return null;
    }

    public function save(CustomerEntity $customer): CustomerEntity
    {
        $this->customers[] = $customer;

        return $customer;
    }
}
