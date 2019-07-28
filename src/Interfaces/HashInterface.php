<?php

namespace Wiring\Interfaces;

interface HashInterface
{
    /**
     * Set password.
     *
     * @param $password
     * @return bool|string
     */
    public function password($password);

    /**
     * Check password.
     *
     * @param $givenPassword
     * @param $knownPassword
     * @return bool
     */
    public function verifyPassword($givenPassword, $knownPassword);

    /**
     * Generate random string.
     *
     * @param int $length
     * @param string $characters
     * @return string
     */
    public function generate($length = 64, $characters);

    /**
     * Get SHA256 hash.
     *
     * @param $input
     * @return string
     */
    public function hash($input);

    /**
     * Check hash.
     *
     * @param $knownHash
     * @param $givenHash
     * @return bool
     */
    public function verifyHash($knownHash, $givenHash);
}
