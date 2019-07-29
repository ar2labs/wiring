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
     * @param bool   $secure
     *
     * @return bool
     */
    public static function set(
        string $name,
        string $value = "",
        int $expiry = 0,
        bool $secure = false
    ): bool;

    /**
     * Checks if a cookie exists.
     *
     * @param $name
     *
     * @return bool
     */
    public static function has($name): bool;

    /**
     * Remove a cookie.
     *
     * @param string $name
     */
    public static function forget(string $name);
}
