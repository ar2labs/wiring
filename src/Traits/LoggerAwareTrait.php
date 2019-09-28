<?php

declare(strict_types=1);

namespace Wiring\Traits;

use BadMethodCallException;
use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Get the current logger.
     *
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set the logger implementation.
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get container logger instance.
     *
     * @throws Exception
     *
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        if (!method_exists($this, 'has')) {
            throw new BadMethodCallException('Container instance not found.');
        }

        if (!$this->has(LoggerInterface::class)) {
            throw new BadMethodCallException('Logger interface not set');
        }

        return $this->get(LoggerInterface::class);
    }
}
