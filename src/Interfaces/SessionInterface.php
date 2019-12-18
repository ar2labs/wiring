<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface SessionInterface
{
    /**
     * Get a session key or a default value.
     *
     * @param string      $key
     * @param string|null $default
     *
     * @return string|array|object
     */
    public static function get(string $key, ?string $default = '');

    /**
     * Defines a session key.
     *
     * @param string      $key
     * @param string|null $value
     *
     * @return string|array|object
     */
    public static function set(string $key, ?string $value);

    /**
     * Checks if a session key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key): bool;

    /**
     * Remove a session key.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function forget(string $key);
}
