<?php

namespace Wiring\Interfaces;

interface AuthInterface
{
    /**
     * Authentication check.
     *
     * @return bool
     */
    public function check();

    /**
     * Get user authentication.
     *
     * @return User|null
     */
    public function user();
}
