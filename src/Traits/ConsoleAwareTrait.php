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
     * @return void
     */
    public function setConsole(ConsoleInterface $console)
    {
        $this->console = $console;
    }

    /**
     * Get console log.
     *
     * @throws \Exception
     *
     * @return ConsoleInterface
     */
    public function console(): ConsoleInterface
    {
        if (!$this->has(ConsoleInterface::class)) {
            throw new \BadFunctionCallException('Console interface not implemented.');
        }

        $console = $this->get(ConsoleInterface::class);

        if (!$console instanceof ConsoleInterface) {
            throw new \UnexpectedValueException('Console interface not implemented.');
        }

        return $console;
    }
}
