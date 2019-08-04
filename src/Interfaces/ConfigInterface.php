<?php

namespace Wiring\Interfaces;

interface ConfigInterface
{
    /**
     * Loads a supported configuration file format.
     *
     * @param array $path
     */
    public function __construct(array $path);

    /**
     * Static method for loading a Config instance.
     *
     * @param string|array $path
     *
     * @return ConfigInterface
     */
    public static function load(?string $path): ConfigInterface;

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @return array|null
     */
    public function all(): ?array;

    /**
     * Gets a value using the offset as a key.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet(string $offset);

    /**
     * Checks if a key exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists(string $offset): bool;

    /**
     * Sets a value using the offset as a key.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet(string $offset, $value): void;

    /**
     * Deletes a key and its value.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset(string $offset): void;

    /**
     * Returns the data array element referenced by its internal cursor.
     *
     * @return mixed The element referenced by the data array's internal cursor.
     *     If the array is empty or there is no element at the cursor, the
     *     function returns false. If the array is undefined, the function
     *     returns null
     */
    public function current();

    /**
     * Returns the data array index referenced by its internal cursor.
     *
     * @return mixed The index referenced by the data array's internal cursor.
     *     If the array is empty or undefined or there is no element at the
     *     cursor, the function returns null
     */
    public function key();

    /**
     * Moves the data array's internal cursor forward one element.
     *
     * @return mixed The element referenced by the data array's internal cursor
     *     after the move is completed. If there are no more elements in the
     *     array after the move, the function returns false. If the data array
     *     is undefined, the function returns null
     */
    public function next();

    /**
     * Moves the data array's internal cursor to the first element.
     *
     * @return mixed The element referenced by the data array's internal cursor
     *     after the move is completed. If the data array is empty, the function
     *     returns false. If the data array is undefined, the function returns
     *     null
     */
    public function rewind();

    /**
     * Tests whether the iterator's current index is valid.
     *
     * @return bool True if the current index is valid; false otherwise
     */
    public function valid(): bool;
}
