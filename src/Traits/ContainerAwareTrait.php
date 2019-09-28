<?php

declare(strict_types=1);

namespace Wiring\Traits;

use BadMethodCallException;
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
     * @throws \Exception   Error while resolving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        if (!$this->container) {
            throw new Exception('Container not found');
        }

        return $this->container->get($id);
    }

    /**
     * Check if the container can return an entry for the given identifier.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        if (!$this->container instanceof ContainerInterface) {
            throw new BadMethodCallException('Container not found');
        }

        if (!method_exists($this->container, 'has')) {
            throw new Exception('Container method not found');
        }

        return $this->container->has($id);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed $value Value, use definition helpers to define objects.
     *
     * @throws \Exception
     *
     * @return ContainerAwareInterface
     */
    public function set(string $name, $value): ContainerAwareInterface
    {
        if (!$this->container instanceof ContainerInterface) {
            throw new BadMethodCallException('Container not found');
        }

        if (!method_exists($this->container, 'set')) {
            throw new Exception('Container method not found');
        }

        return $this->container->set($name, $value);
    }

    /**
     * Resolves an entry by its name.
     * If given a class name, it will return a new instance of that class.
     *
     * @param string $name  Entry name or a class name.
     * @param array $params Optional parameters to use to build the entry.
     *
     * @throws \Exception   Error while resolving the entry.
     *
     * @return mixed
     */
    public function make(string $name, array $params = [])
    {
        if (!$this->container instanceof ContainerInterface) {
            throw new Exception('Container not found');
        }

        if (!method_exists($this->container, 'make')) {
            throw new BadMethodCallException('Container method not found');
        }

        return $this->container->make($name, $params);
    }

    /**
     * Call the given function using the given parameters.
     *
     * Missing parameters will be resolved from the container.
     *
     * @param callable $callable Function to call.
     * @param array $params  Parameters to use.
     *
     * @throws \Exception
     *
     * @return mixed Result of the function.
     */
    public function call(callable $callable, array $params = [])
    {
        if (!$this->container instanceof ContainerInterface) {
            throw new Exception('Container not found');
        }

        if (!method_exists($this->container, 'call')) {
            throw new BadMethodCallException('Container method not found');
        }

        return $this->container->call($callable, $params);
    }
}
