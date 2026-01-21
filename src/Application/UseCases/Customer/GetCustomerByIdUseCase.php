<?php

namespace Src\Application\UseCases\Customer;

use Src\Contracts\DTOs\Customer\CustomerResponseDTO;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class GetCustomerByIdUseCase {
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository) {
        $this->customerRepository = $customerRepository;
    }

    public function run(int $id): CustomerResponseDTO|null {
        $customerEntity = $this->customerRepository->getById($id);

        if ($customerEntity === null) {
            return null;
        }

        return CustomerResponseDTO::fromEntity($customerEntity);
    }
}
