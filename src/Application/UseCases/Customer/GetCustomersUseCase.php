<?php

namespace Src\Application\UseCases\Customer;

use Src\Contracts\DTOs\Customer\CustomerResponseDTO;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class GetCustomersUseCase {
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository) {
        $this->customerRepository = $customerRepository;
    }

    public function run(): array {
        $customersEntities = $this->customerRepository->getAll();

        $customers = [];

        foreach ($customersEntities as $customerEntity) {
            $customers[] = CustomerResponseDTO::fromEntity($customerEntity);
        }

        return $customers;
    }
}
