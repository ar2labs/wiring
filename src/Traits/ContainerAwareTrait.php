<?php

declare(strict_types=1);

namespace Wiring\Traits;

use BadMethodCallException;
use Psr\Container\ContainerInterface;

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
     * @return void
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
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
            throw new BadMethodCallException('Method get does not exist.');
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
        if ((!$this->container) || (!method_exists($this->container, 'has'))) {
            throw new BadMethodCallException('Method has does not exist.');
        }

        return $this->container->has($id);
    }
}
