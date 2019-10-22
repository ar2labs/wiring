<?php

namespace Wiring\Interfaces;

interface ControllerInterface
{
    /**
     * Get an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     */
    public function get(string $id);

    /**
     * Check if the container can return an entry for the given identifier.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Return database connection.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function database();
}
