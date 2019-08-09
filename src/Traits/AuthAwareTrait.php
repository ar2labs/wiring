<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Exception;
use Wiring\Interfaces\AuthInterface;

trait AuthAwareTrait
{
    /**
     * @var AuthInterface|null
     */
    protected $auth;

    /**
     * Get the current authentication.
     *
     * @return AuthInterface|null
     */
    public function getAuth(): ?AuthInterface
    {
        return $this->auth;
    }

    /**
     * Set the authentication implementation.
     *
     * @param AuthInterface $auth
     *
     * @return self
     */
    public function setAuth(AuthInterface $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get container authentication instance.
     *
     * @throws Exception
     *
     * @return AuthInterface
     */
    public function auth(): AuthInterface
    {
        if (!method_exists($this, 'has')) {
            throw new Exception('Container instance not found.');
        }

        if (!$this->has(AuthInterface::class)) {
            throw new Exception('Auth interface not set');
        }

        return $this->get(AuthInterface::class);
    }
}
