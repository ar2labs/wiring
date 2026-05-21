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
    public function database(string $connection = '')
    {
        if (!$this->has(DatabaseInterface::class)) {
            throw new \BadFunctionCallException('Database interface not implemented.');
        }

        $database = $this->get(DatabaseInterface::class);

        if (!$database instanceof DatabaseInterface) {
            throw new \UnexpectedValueException('Database interface not implemented.');
        }

        if (!empty($connection)) {
            return $database->connection($connection);
        }

        return $database;
    }
}
