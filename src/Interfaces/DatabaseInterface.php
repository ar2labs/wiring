<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface DatabaseInterface
{
    /**
     * Database connection method.
     *
     * @return void
     */
    public function connect();

    /**
     * Data query method.
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function query(QueryInterface $query);
}
