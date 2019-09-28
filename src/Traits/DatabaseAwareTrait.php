<?php

declare(strict_types=1);

namespace Wiring\Traits;

use BadMethodCallException;
use Wiring\Interfaces\DatabaseInterface;

trait DatabaseAwareTrait
{
    /**
     * @var DatabaseInterface|null
     */
    protected $database;

    /**
     * Get the current database.
     *
     * @return DatabaseInterface|null
     */
    public function getDatabase(): ?DatabaseInterface
    {
        return $this->database;
    }

    /**
     * Set the database implementation.
     *
     * @param DatabaseInterface $database
     *
     * @return self
     */
    public function setDatabase(DatabaseInterface $database): self
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Get container database instance.
     *
     * @throws \Exception
     *
     * @return DatabaseInterface
     */
    public function database(): DatabaseInterface
    {
        if (!method_exists($this, 'has')) {
            throw new BadMethodCallException('Container instance not found.');
        }

        if (!$this->has(DatabaseInterface::class)) {
            throw new BadMethodCallException('Database interface not defined.');
        }

        return $this->get(DatabaseInterface::class);
    }
}
