<?php

namespace Src\Core;

use ReflectionClass;
use ReflectionException;
use Exception;

class Container
{
    /**
     * @var array<string, mixed> Bindings of interfaces to implementations
     */
    private array $bindings = [];

    /**
     * @var array<string, bool> Singleton flags
     */
    private array $singletons = [];

    /**
     * @var array<string, object> Singleton instances
     */
    private array $instances = [];

    /**
     * Bind an interface to a concrete implementation
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @return void
     */
    public function bind(string $abstract, callable|string|null $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = false;
    }

    /**
     * Bind a singleton instance
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = true;
    }

    /**
     * Resolve a class from the container
     *
     * @param string $abstract
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function make(string $abstract): mixed
    {
        // Check if we have a singleton instance
        if (($this->singletons[$abstract] ?? false) && isset($this->instances[$abstract])) {
            return $this->instances[$abstract]; // Return
        }

        // Get the concrete implementation
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // If concrete is a callable, call it
        if (is_callable($concrete)) {
            $instance = $concrete($this);
        } else {
            $instance = $this->build($concrete);
        }

        // Store singleton instances
        if ($this->singletons[$abstract] ?? false) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a concrete class instance with dependency injection
     *
     * @param string $concrete
     * @return object
     * @throws ReflectionException
     * @throws Exception
     */
    private function build(string $concrete): object
    {
        $reflection = new ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null || !$type instanceof \ReflectionNamedType) {
                throw new Exception("Cannot resolve parameter {$parameter->getName()} in class {$concrete}");
            }

            $typeName = $type->getName();
            $dependencies[] = $this->make($typeName);
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
