<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface CsrfInterface
{
    /**
     * Retrieve token name key
     *
     * @return string|null
     */
    public function getTokenNameKey(): ?string;

    /**
     * Retrieve token value key
     *
     * @return string|null
     */
    public function getTokenValueKey(): ?string;

    /**
     * @param $prefix
     * @param $storage
     *
     * @return mixed
     */
    public function validateStorage();

    /**
     * Generates a new CSRF token
     *
     * @return array
     */
    public function generateToken(): array;

    /**
     * Generates a new CSRF token and attaches it to the Request Object
     *
     * @param ServerRequestInterface $request PSR7 response object.
     *
     * @return ServerRequestInterface          PSR7 response object.
     */
    public function generateNewToken(
        ServerRequestInterface $request
    ): ServerRequestInterface;

    /**
     * Validate CSRF token from current request
     * against token value stored in $_SESSION
     *
     * @param string $name  CSRF name
     * @param string $value CSRF token value
     *
     * @return bool
     */
    public function validateToken(string $name, string $value): bool;
}
