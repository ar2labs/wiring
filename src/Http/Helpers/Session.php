<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

use Wiring\Interfaces\SessionInterface;

class Session implements SessionInterface
{
    /**
     * Get a session key or a default value.
     *
     * @param string $key
     * @param string|null $default
     * @return null
     */
    public static function get($key, $default = null)
    {
        if (self::has($key)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * Defines a session key.
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public static function set($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    /**
     * Checks if a session key exists.
     *
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return (isset($_SESSION[$key])) ? true : false;
    }

    /**
     * Remove a session key.
     *
     * @param $key
     */
    public static function forget($key)
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }
}
