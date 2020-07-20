<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface QueryInterface
{
    /**
     * Define the database query.
     *
     * @param string $query
     */
    public function set(string $query): void;
}
