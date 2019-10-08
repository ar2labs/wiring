<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\HashInterface;

trait HashAwareTrait
{
    /**
     * @var HashInterface|null
     */
    protected $hash;

    /**
     * Get the current hash.
     *
     * @return HashInterface|null
     */
    public function getHash(): ?HashInterface
    {
        return $this->hash;
    }

    /**
     * Set the hash implementation.
     *
     * @param HashInterface $hash
     *
     * @return self
     */
    public function setHash(HashInterface $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get container hash instance.
     *
     * @throws \Exception
     *
     * @return HashInterface|null
     */
    public function hash(): ?HashInterface
    {
        if (!$this->has(HashInterface::class)) {
            throw new \Exception('Hash interface not implemented.');
        }

        return $this->get(HashInterface::class);
    }
}
