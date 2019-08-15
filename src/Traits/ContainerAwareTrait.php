<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Exception;
use Psr\Container\ContainerInterface;
use Wiring\Interfaces\ContainerAwareInterface;

trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * Get container.
     *
     * @return ContainerInterface
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * Set container.
     *
     * @param ContainerInterface $container
     *
     * @return ContainerAwareInterface
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws Exception   Error while resolving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        $container = $this->getContainer();

        if (!$container) {
            throw new Exception('Container not found');
        }

        return $container->get($id);
    }

    /**
     * Check if the container can return an entry for the given identifier.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        $container = $this->getContainer();

        return $container->has($id);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed $value Value, use definition helpers to define objects.
     *
     * @throws Exception
     *
     * @return ContainerAwareInterface
     */
    public function set(string $name, $value): ContainerAwareInterface
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'set')) {
            throw new Exception('Container method not found');
        }

        return $container->set($name, $value);
    }

    /**
     * Resolves an entry by its name.
     * If given a class name, it will return a new instance of that class.
     *
     * @param string $name  Entry name or a class name.
     * @param array $params Optional parameters to use to build the entry.
     *
     * @throws Exception    Error while resolving the entry.
     *
     * @return mixed
     */
    public function make(string $name, array $params = [])
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'make')) {
            throw new Exception('Container method not found');
        }

        return $container->make($name, $params);
    }

    /**
     * Call the given function using the given parameters.
     *
     * Missing parameters will be resolved from the container.
     *
     * @param callable $callable Function to call.
     * @param array $params  Parameters to use.
     *
     * @throws Exception
     *
     * @return mixed Result of the function.
     */
    public function call(callable $callable, array $params = [])
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'call')) {
            throw new Exception('Container method not found');
        }

        return $container->call($callable, $params);
    }
}
