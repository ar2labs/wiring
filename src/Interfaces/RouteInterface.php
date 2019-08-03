<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Container\ContainerInterface;

interface RouterInterface
{
    /**
     * Invoke the controller callable based on the abstract strategy.
     *
     * @param ContainerInterface|null $container
     *
     * @throws InvalidArgumentException
     *
     * @return callable
     */
    public function getCallable(?ContainerInterface $container = null): callable;

    /**
     * Return variables to be passed to route callable.
     *
     * @return array
     */
    public function getVars(): array;

    /**
     * Set variables to be passed to route callable.
     *
     * @param array $vars
     *
     * @return self
     */
    public function setVars(array $vars): self;
}
