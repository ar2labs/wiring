<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

use Wiring\Interfaces\SessionInterface;

class Session implements SessionInterface
{
    /**
     * Get a session key or a default value.
     *
     * @param string      $key
     * @param string|null $default
     *
     * @return mixed
     */
    public static function get(string $key, ?string $default = '')
    {
        if (self::has($key)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * Defines a session key.
     *
     * @param string      $key
     * @param string|null $value
     *
     * @return mixed
     */
    public static function set(string $key, ?string $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * Checks if a session key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key): bool
    {
        return (isset($_SESSION[$key])) ? true : false;
    }

    /**
     * Remove a session key.
     *
     * @param string $key
     */
    public static function forget(string $key)
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }
}
