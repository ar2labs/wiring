<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface DatabaseInterface
{
    /**
     * Get connection instance.
     *
     * @param string $dbname
     *
     * @return mixed
     */
    public function connection(string $dbname = 'default');
}
