<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface DatabaseInterface
{
    /**
     * Get database instance.
     *
     * @param string $connection
     * @throws \Exception
     *
     * @return mixed
     */
    public function database(string $connection = 'default');
}
