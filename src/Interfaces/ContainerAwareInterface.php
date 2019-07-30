<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * Get the current container
     *
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface;

    /**
     * Set the container implementation
     *
     * @param ContainerInterface $container
     *
     * @return ContainerAwareInterface
     */
    public function setContainer(
        ContainerInterface $container
    ): ContainerAwareInterface;
}
