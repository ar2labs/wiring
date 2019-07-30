<?php

declare(strict_types=1);

namespace Wiring\Traits;

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
}