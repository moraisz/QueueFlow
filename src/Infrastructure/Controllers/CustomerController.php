<?php

namespace Src\Infrastructure\Controllers;

use Src\Contracts\Interfaces\Repositories\CustomerRepositoryInterface;
use Src\Infrastructure\Http\Request;
use Src\Infrastructure\Http\Response;
use Src\Infrastructure\Http\View;
use Src\Application\UseCases\Customer\CreateCustomerUseCase;
use Src\Application\UseCases\Customer\GetCustomerByIdUseCase;
use Src\Application\UseCases\Customer\GetCustomersUseCase;
use Src\Contracts\DTOs\Customer\CustomerCreateDTO;

class CustomerController
{
    /**
     * Repositório injetado via construtor
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * Container injeta automaticamente as dependências
     */
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function get(Request $request, Response $response): Response
    {
        $customerUseCase = new GetCustomersUseCase($this->customerRepository);
        $customers = $customerUseCase->run();

        if ($request->isJson()) {
            return $response->json($customers ?? [], $customers ? 200 : 404);
        }

        $html = View::render('pages/customers', [
            'customers' => $customers,
            'message' => 'Bem-vindo!',
            'title' => 'Perfil do Cliente',
        ]);

        return $response->html($html, 200);
    }

    public function getUnique(Request $request, Response $response): Response
    {
        $customerUseCase = new GetCustomerByIdUseCase($this->customerRepository);
        $id = (int) $request->getParam('id');
        $customer = $customerUseCase->run($id);

        if ($request->isJson()) {
            return $response->json($customer ?? [], $customer ? 200 : 404);
        }

        $html = View::render('pages/customers', [
            'customer' => $customer,
            'message' => 'Bem-vindo!',
            'title' => 'Perfil do Cliente',
        ]);

        return $response->html($html, 200);
    }

    public function post(Request $request, Response $response): Response
    {
        $customerUseCase = new CreateCustomerUseCase($this->customerRepository);
        $customerDTO = new CustomerCreateDTO(
            $request->getBody('name'),
            $request->getBody('priority'),
            $request->getBody('type'),
            $request->getBody('status'),
            $request->getBody('email'),
            $request->getBody('telephone'),
        );
        $customer = $customerUseCase->run($customerDTO);

        if ($request->isJson()) {
            return $response->json($customer ?? [], $customer ? 200 : 404);
        }

        $html = View::render('pages/customers', [
            'user' => $customer,
            'message' => 'Bem-vindo!',
            'title' => 'Perfil do Cliente',
        ]);

        return $response->html($html, 200);
    }

    public function put(Request $request, Response $response): Response
    {
        return $response;
    }

    public function delete(Request $request, Response $response): Response
    {
        return $response;
    }
}
