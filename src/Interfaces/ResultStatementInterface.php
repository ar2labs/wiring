<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ResultStatementInterface extends \Traversable
{
    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean
     */
    public function closeCursor();

    /**
     * Returns the number of columns in the result set.
     *
     * @return integer
     */
    public function columnCount();

    /**
     * Sets the fetch mode to use while iterating this statement.
     *
     * @param integer $fetchMode The fetch mode must be one of the
     *                           PDO::FETCH_* constants.
     * @param mixed $arg2
     * @param mixed $arg3
     *
     * @return boolean
     *
     * @see PDO::FETCH_* constants.
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null);

    /**
     * Returns the next row of a result set.
     *
     * @param integer|null $fetchMode Controls how the next row will be returned
     *                                to the caller. The value must be one of
     *                                the PDO::FETCH_* constants, defaulting to
     *                                PDO::FETCH_BOTH.
     *
     * @return mixed                  The return value of this method on success
     *                                depends on the fetch mode. In all cases,
     *                                FALSE is returned on failure.
     *
     * @see PDO::FETCH_* constants.
     */
    public function fetch($fetchMode = null);

    /**
     * Returns an array containing all of the result set rows.
     *
     * @param integer|null $fetchMode Controls how the next row will be returned
     *                                to the caller. The value must be one of
     *                                the PDO::FETCH_* constants, defaulting to
     *                                PDO::FETCH_BOTH.
     *
     * @return array
     *
     * @see PDO::FETCH_* constants.
     */
    public function fetchAll($fetchMode = null);

    /**
     * Returns a single column from the next row of a result set or FALSE if
     * there are no more rows.
     *
     * @param integer $columnIndex 0-indexed number of the column you wish to
     *                             retrieve from the row.
     *                             If no value is supplied,
     *                             PDOStatement->fetchColumn() fetches the
     *                             first column.
     *
     * @return string|boolean      A single column in the next row of a result
     *                             set, or FALSE if there are no more rows.
     */
    public function fetchColumn($columnIndex = 0);
}
