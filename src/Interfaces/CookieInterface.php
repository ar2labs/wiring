<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface CookieInterface
{
    /**
     * Get a cookie.
     *
     * @param string $name
     *
     * @return string|array|object
     */
    public static function get(string $name);

    /**
     * Set a cookie.
     *
     * @param string $name
     * @param string $value
     * @param int    $expiry
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return bool
     */
    public static function set(
        string $name,
        string $value = '',
        int $expiry = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = true
    );

    /**
     * Checks if a cookie exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool;

    /**
     * Remove a cookie.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function forget(string $name): bool;
}
