<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\ConsoleInterface;

trait ConsoleAwareTrait
{
    /**
     * @var ConsoleInterface|null
     */
    protected $console;

    /**
     * Get the current console.
     *
     * @return ConsoleInterface|null
     */
    public function getConsole(): ?ConsoleInterface
    {
        return $this->console;
    }

    /**
     * Set the console implementation.
     *
     * @param ConsoleInterface $console
     *
     * @return self
     */
    public function setConsole(ConsoleInterface $console): self
    {
        $this->console = $console;

        return $this;
    }

    /**
     * Get console log.
     *
     * @throws Exception
     *
     * @return ConsoleInterface
     */
    public function console(): ConsoleInterface
    {
        if (method_exists($this, 'has')) {
            throw new Exception('Container instance not found.');
        }

        if (!$this->has(ConsoleInterface::class)) {
            throw new Exception('Console interface not defined.');
        }

        return $this->get(ConsoleInterface::class);
    }
}
