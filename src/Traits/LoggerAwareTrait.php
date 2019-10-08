<?php

declare(strict_types=1);

namespace Wiring\Traits;

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
     * @throws \Exception
     *
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        if (!$this->has(LoggerInterface::class)) {
            throw new \BadFunctionCallException('Logger interface not implemented.');
        }

        return $this->get(LoggerInterface::class);
    }
}
