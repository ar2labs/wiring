<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

use Wiring\Interfaces\CookieInterface;

class Cookie implements CookieInterface
{
    /**
     * Get a cookie.
     *
     * @param string $name
     *
     * @return string|array|object
     */
    public static function get(string $name)
    {
        return $_COOKIE[$name];
    }

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
    ): bool {
        return setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
    }

    /**
     * Checks if a cookie exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
     * Remove a cookie.
     *
     * @param string $name
     *
     * @return void
     */
    public static function forget(string $name): void
    {
        if (self::has($name)) {
            self::set($name, '', time() - 1);
        }
    }
}
