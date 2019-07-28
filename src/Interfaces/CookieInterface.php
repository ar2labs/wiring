<?php

namespace Wiring\Interfaces;

interface CookieInterface
{
    /**
     * Get cookie.
     *
     * @param $name
     * @return mixed
     */
    public static function get($name);

    /**
     * Set cookie.
     *
     * @param $name
     * @param $value
     * @param $expiry
     * @param bool $secure
     * @return bool
     */
    public static function set($name, $value, $expiry, $secure = false);

    /**
     * Check cookie exists.
     *
     * @param $name
     * @return bool
     */
    public static function has($name);

    /**
     * Remove cookie.
     *
     * @param $name
     */
    public static function forget($name);
}
