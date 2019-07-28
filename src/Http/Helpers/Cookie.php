<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Cookie
{
    /**
     * Get a cookie.
     *
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        return $_COOKIE[$name];
    }

    /**
     * Set a cookie.
     *
     * @param $name
     * @param $value
     * @param $expiry
     * @param bool $secure
     * @return bool
     */
    public static function set($name, $value, $expiry, $secure = false)
    {
        if (setcookie($name, $value, $expiry, '/', null, $secure, true)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a cookie exists.
     *
     * @param $name
     * @return bool
     */
    public static function has($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
     * Remove a cookie.
     *
     * @param $name
     */
    public static function forget($name)
    {
        if (self::has($$name)) {
            self::set($name, '', time() - 1);
        }
    }
}
