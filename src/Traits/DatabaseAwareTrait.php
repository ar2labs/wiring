<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\DatabaseInterface;

trait DatabaseAwareTrait
{
    /**
     * Get database instance.
     *
     * @param string $connection
     * @throws \Exception
     *
     * @return mixed
     */
    public function database(string $connection = 'default')
    {
        if (!$this->has(DatabaseInterface::class)) {
            throw new \BadFunctionCallException('Database interface not implemented.');
        }

        // Check connection method exist
        if (method_exists($this->get(DatabaseInterface::class), 'connection')) {
            return $this->get(DatabaseInterface::class)
                ->connection($connection);
        }

        return $this->get(DatabaseInterface::class);
    }
}
