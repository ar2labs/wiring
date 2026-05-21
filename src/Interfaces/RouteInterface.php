<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Container\ContainerInterface;

interface RouteInterface
{
    /**
     * Invoke the controller callable based on the abstract strategy.
     *
     * @param ContainerInterface|null $container
     *
     * @throws \InvalidArgumentException
     *
     * @return callable
     */
    public function getCallable(?ContainerInterface $container = null): callable;

    /**
     * Return variables to be passed to route callable.
     *
    * @return array<string, mixed>
     */
    public function getVars(): array;

    /**
     * Set variables to be passed to route callable.
     *
    * @param array<string, mixed> $vars
     *
     * @return self
     */
    public function setVars(array $vars): self;
}
