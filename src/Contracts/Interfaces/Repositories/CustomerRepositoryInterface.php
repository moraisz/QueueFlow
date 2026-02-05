<?php

namespace Src\Contracts\Interfaces\Repositories;

use Src\Application\Entities\CustomerEntity;

interface CustomerRepositoryInterface
{
    public function getAll(): array;
    public function getById(int $id): ?CustomerEntity;
    public function save(CustomerEntity $customer): CustomerEntity;
}
