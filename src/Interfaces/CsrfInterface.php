<?php

namespace Wiring\Interfaces;

interface CsrfInterface
{
    /**
     * Retrieve token name key
     *
     * @return string
     */
    public function getTokenNameKey();

    /**
     * Retrieve token value key
     *
     * @return string
     */
    public function getTokenValueKey();

    /**
     * @param $prefix
     * @param $storage
     * @return mixed
     */
    public function validateStorage();

    /**
     * Generates a new CSRF token
     *
     * @return array
     */
    public function generateToken();

    /**
     * Generates a new CSRF token and attaches it to the Request Object
     *
     * @param  ServerRequestInterface $request PSR7 response object.
     *
     * @return ServerRequestInterface PSR7 response object.
     */
    public function generateNewToken(ServerRequestInterface $request);

    /**
     * Validate CSRF token from current request
     * against token value stored in $_SESSION
     *
     * @param  string $name  CSRF name
     * @param  string $value CSRF token value
     *
     * @return bool
     */
    public function validateToken($name, $value);
}
