<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface DatabaseInterface
{
    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @param string $prepareString
     *
     * @return StatementInterface
     */
    public function prepare(string $prepareString): StatementInterface;

    /**
     * Executes an SQL statement, returning a result set as a Statement object.
     *
     * @return StatementInterface
     */
    public function query(): StatementInterface;

    /**
     * Quotes a string for use in a query.
     *
     * @param string $input
     * @param integer $type
     *
     * @return string
     */
    public function quote(string $input, $type = \PDO::PARAM_STR): string;

    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @param string $statement
     *
     * @return integer
     */
    public function exec($statement): int;

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string|null $name
     *
     * @return string
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * Initiates a transaction.
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function beginTransaction(): bool;

    /**
     * Commits a transaction.
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function commit(): bool;

    /**
     * Rolls back the current transaction, as initiated by beginTransaction().
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function rollBack();

    /**
     * Returns the error code associated with the last operation on the
     * database handle.
     *
     * @return string|null The error code, or null if no
     * operation has been run on the database handle.
     */
    public function errorCode(): ?string;

    /**
     * Returns extended error information associated with the last operation on
     * the database handle.
     *
     * @return array
     */
    public function errorInfo(): array;
}
