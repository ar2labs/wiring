<?php

declare(strict_types=1);

namespace Wiring\Strategy;

use Wiring\Interfaces\StrategyAwareInterface;
use Wiring\Interfaces\StrategyInterface;

trait StrategyAwareTrait
{
    /**
     * @var StrategyInterface
     */
    protected $strategy;

    /**
     * Get the current strategy.
     *
     * @return StrategyInterface
     */
    public function getStrategy(): ?StrategyInterface
    {
        return $this->strategy;
    }

    /**
     * Set the strategy implementation.
     *
     * @param StrategyInterface $strategy
     *
     * @return StrategyAwareInterface
     */
    public function setStrategy(StrategyInterface $strategy): StrategyAwareInterface
    {
        $this->strategy = $strategy;

        return $this;
    }
}
