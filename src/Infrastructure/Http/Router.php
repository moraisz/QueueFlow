<?php

namespace Src\Infrastructure\Http;

use Src\Core\Container;

class Router
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param callable(): mixed|mixed[] $callback
     */
    public function get(string $path, callable|array $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * @param callable(): mixed|mixed[] $callback
     */
    public function post(string $path, callable|array $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * @param callable(): mixed|mixed[] $callback
     */
    public function put(string $path, callable|array $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * @param callable(): mixed|mixed[] $callback
     */
    public function delete(string $path, callable|array $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * @param callable(): mixed|mixed[] $callback
     */
    private function addRoute(
        string $method,
        string $path,
        callable|array $callback,
    ): void {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback,
        ];
    }

    public function run(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if (
                $route['method'] === $request->getMethod()
                    && preg_match($route['pattern'], $request->getPath(), $matches)
            ) {
                // Remove indexed matches, keep only named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Create a response object
                $response = new Response();

                // Execute callback
                if (is_callable($route['callback'])) {
                    $result = call_user_func($route['callback'], $request, $response);

                    // If the callback returned a Response, use it
                    if ($result instanceof Response) {
                        return $result;
                    }

                    // Otherwise, return the response that was passed
                    return $response;
                } elseif (is_array($route['callback'])) {
                    // If [Controller::class, 'method']
                    [$controllerClass, $methodName] = $route['callback'];
                    $controller = $this->container->make($controllerClass);
                    $result = $controller->$methodName($request, $response);

                    // If the callback returned a Response, use it
                    if ($result instanceof Response) {
                        return $result;
                    }

                    // Otherwise, return the response that was passed
                    return $response;
                }
            }
        }

        // Route not found
        return (new Response())->json(['error' => 'Route not found'], 404);
    }
}
