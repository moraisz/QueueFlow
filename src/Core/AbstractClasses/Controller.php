<?php

namespace Src\Core\AbstractClasses;

use Src\Core\Request;
use Src\Core\Response;
use Src\Core\View;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @param array<int,mixed> $data
     */
    protected function renderView(string $view, array $data = [], int $statusCode = 200): Response
    {
        $html = View::render($view, $data);
        return $this->response->html($html, $statusCode);
    }

    /**
     * @param array<int,mixed> $data
     */
    protected function jsonResponse(array $data, int $statusCode = 200): Response
    {
        return $this->response->json($data, $statusCode);
    }
}
