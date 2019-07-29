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
    public function prepare($prepareString): StatementInterface;

    /**
     * Executes an SQL statement, returning a result set as a Statement object.
     *
     * @return StatementInterface
     */
    public function query();

    /**
     * Quotes a string for use in a query.
     *
     * @param string $input
     * @param integer $type
     *
     * @return string
     */
    public function quote($input, $type = \PDO::PARAM_STR);

    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @param string $statement
     *
     * @return integer
     */
    public function exec($statement);

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string|null $name
     *
     * @return string
     */
    public function lastInsertId($name = null);

    /**
     * Initiates a transaction.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function beginTransaction();

    /**
     * Commits a transaction.
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function commit();

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
    public function errorCode();

    /**
     * Returns extended error information associated with the last operation on
     * the database handle.
     *
     * @return array
     */
    public function errorInfo();
}
