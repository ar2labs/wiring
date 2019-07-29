<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Wiring\Interfaces\StrategyInterface;

interface StrategyAwareInterface
{
    /**
     * Get the current strategy.
     *
     * @return StrategyInterface
     */
    public function getStrategy(): ?StrategyInterface;

    /**
     * Set the strategy implementation.
     *
     * @param StrategyInterface $strategy
     *
     * @return static
     */
    public function setStrategy(
        StrategyInterface $strategy
    ): StrategyAwareInterface;
}
