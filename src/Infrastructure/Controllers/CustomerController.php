<?php

namespace Src\Infrastructure\Controllers;

use Src\Core\AbstractClasses\Controller;
use Src\Core\Response;
use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;
use Src\Application\UseCases\Customer\CreateCustomerUseCase;
use Src\Application\UseCases\Customer\GetCustomerByIdUseCase;
use Src\Application\UseCases\Customer\GetCustomersUseCase;
use Src\Contracts\DTOs\Customer\CustomerCreateDTO;

class CustomerController extends Controller
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function get(): Response
    {
        $customerUseCase = new GetCustomersUseCase($this->customerRepository);
        $customers = $customerUseCase->run();

        if ($this->request->isJson()) {
            return $this->jsonResponse($customers ?? [], $customers ? 200 : 404);
        }

        return $this->renderView('pages/customers', [
            'customers' => $customers,
            'message' => 'Bem-vindo!',
            'title' => 'Perfil do Cliente',
        ]);
    }

    public function getUnique(): Response
    {
        $customerUseCase = new GetCustomerByIdUseCase($this->customerRepository);
        $id = (int) $this->request->getParam('id');
        $customer = $customerUseCase->run($id);

        if ($this->request->isJson()) {
            return $this->jsonResponse($customer ?? [], $customer ? 200 : 404);
        }
    }

    public function post(): Response
    {
        $customerUseCase = new CreateCustomerUseCase($this->customerRepository);
        $customerCreateDTO = CustomerCreateDTO::fromRequest($this->request);
        $customer = $customerUseCase->run($customerCreateDTO);

        if ($this->request->isJson()) {
            return $this->jsonResponse($customer ?? [], $customer ? 200 : 404);
        }
    }

    public function put(): Response
    {
        return $this->response;
    }

    public function delete(): Response
    {
        return $this->response;
    }
}
