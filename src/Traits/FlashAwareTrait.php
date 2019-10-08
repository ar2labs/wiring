<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\FlashInterface;

trait FlashAwareTrait
{
    /**
     * @var FlashInterface|null
     */
    protected $flash;

    /**
     * Get the current flash.
     *
     * @return FlashInterface|null
     */
    public function getFlash(): ?FlashInterface
    {
        return $this->flash;
    }

    /**
     * Set the flash implementation.
     *
     * @param FlashInterface $flash
     *
     * @return self
     */
    public function setFlash(FlashInterface $flash): self
    {
        $this->flash = $flash;

        return $this;
    }

    /**
     * Get flash log.
     *
     * @throws \Exception
     *
     * @return FlashInterface
     */
    public function flash(): FlashInterface
    {
        if (!$this->has(FlashInterface::class)) {
            throw new \BadFunctionCallException('Flash interface not implemented.');
        }

        return $this->get(FlashInterface::class);
    }
}
