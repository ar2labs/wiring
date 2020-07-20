<?php

declare(strict_types=1);

namespace Wiring\Traits;

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
     * @return void
     */
    public function setAuth(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get container authentication instance.
     *
     * @throws \Exception
     *
     * @return AuthInterface
     */
    public function auth(): AuthInterface
    {
        if (!$this->has(AuthInterface::class)) {
            throw new \BadFunctionCallException('Auth interface not implemented.');
        }

        return $this->get(AuthInterface::class);
    }
}
