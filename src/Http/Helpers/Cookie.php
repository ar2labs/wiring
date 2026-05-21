<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

use Wiring\Interfaces\CookieInterface;

class Cookie implements CookieInterface
{
    private const SAME_SITE_LAX = 'Lax';

    /**
     * Get a cookie.
     *
     * @param string $name
     *
     * @return string|array<mixed>|object
     */
    public static function get(string $name)
    {
        $value = $_COOKIE[$name] ?? '';

        if (is_string($value) || is_array($value) || is_object($value)) {
            return $value;
        }

        return '';
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
        return setcookie($name, $value, self::createCookieOptions($expiry, $path, $domain, $secure, $httponly));
    }

    /**
    * @return array{expires: int, path: string, domain?: string, secure: bool, httponly: bool, samesite: 'Lax'}
     */
    protected static function createCookieOptions(
        int $expiry,
        string $path,
        string $domain,
        bool $secure,
        bool $httponly
    ): array {
        $options = [
            'expires' => $expiry,
            'path' => $path,
            'secure' => $secure || self::isHttpsRequest(),
            'httponly' => $httponly,
            'samesite' => self::SAME_SITE_LAX,
        ];

        if ($domain !== '') {
            $options['domain'] = $domain;
        }

        return $options;
    }

    protected static function isHttpsRequest(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';
        if (is_string($https) && $https !== '' && strtolower($https) !== 'off') {
            return true;
        }

        $serverPort = $_SERVER['SERVER_PORT'] ?? null;

        return $serverPort === 443 || $serverPort === '443';
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
        return isset($_COOKIE[$name]);
    }

    /**
     * Remove a cookie.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function forget(string $name): bool
    {
        if (self::has($name)) {
            return self::set($name, '', time() - 1);
        }

        return false;
    }
}
