<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\SessionInterface;

trait SessionAwareTrait
{
    /**
     * @var SessionInterface|null
     */
    protected $session;

    /**
     * Get the current session.
     *
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface
    {
        return $this->session;
    }

    /**
     * Set the session implementation.
     *
     * @param SessionInterface $session
     *
     * @return self
     */
    public function setSession(SessionInterface $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get container session instance.
     *
     * @throws \Exception
     *
     * @return SessionInterface
     */
    public function session(): SessionInterface
    {
        if (!$this->has(SessionInterface::class)) {
            throw new \Exception('Session interface not implemented.');
        }

        return $this->get(SessionInterface::class);
    }
}
