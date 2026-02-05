<?php

namespace Src\Application\UseCases\Customer;

use Src\Application\Entities\CustomerEntity;
use Src\Contracts\DTOs\Customer\CustomerCreateDTO;
use Src\Contracts\DTOs\Customer\CustomerResponseDTO;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;

class CreateCustomerUseCase
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function run(CustomerCreateDTO $customerDTO): CustomerResponseDTO
    {
        $customer = new CustomerEntity(
            $customerDTO->name,
            $customerDTO->priority,
            $customerDTO->type,
            $customerDTO->status,
            $customerDTO->email,
            $customerDTO->telephone,
            4
        );

        $customer = $this->customerRepository->save($customer);

        return CustomerResponseDTO::fromEntity($customer);
    }
}
