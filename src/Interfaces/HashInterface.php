<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface HashInterface
{
    /**
     * Set a password.
     *
     * @param string $password
     *
     * @return bool
     */
    public function password(string $password): bool;

    /**
     * Check a password.
     *
     * @param string $givenPassword
     * @param string $knownPassword
     *
     * @return bool
     */
    public function verifyPassword(
        string $givenPassword,
        string $knownPassword
    ): bool;

    /**
     * Generate a random string.
     *
     * @param string $characters
     * @param int $length
     *
     * @return string
     */
    public function generate(string $characters, int $length = 64): string;

    /**
     * Get a SHA256 hash.
     *
     * @param string $input
     *
     * @return string
     */
    public function hash(string $input): string;

    /**
     * Check a hash.
     *
     * @param string $knownHash
     * @param string $givenHash
     *
     * @return bool
     */
    public function verifyHash($knownHash, $givenHash): bool;
}
