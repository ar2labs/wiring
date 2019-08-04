<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface AuthInterface
{
    /**
     * Authentication check.
     *
     * @return bool
     */
    public function check(): bool;

    /**
     * Get user authentication.
     *
     * @return object|null
     */
    public function user(): ?object;
}
