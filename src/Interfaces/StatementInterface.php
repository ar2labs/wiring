<?php

namespace Wiring\Interfaces;

interface StatementInterface extends ResultStatementInterface
{
    /**
     * Binds a value to a corresponding named or positional placeholder in the
     * SQL statement that was used to prepare the statement.
     *
     * @param mixed $param Parameter identifier.
     * @param mixed $value The value to bind to the parameter.
     * @param integer $type Explicit data type for the parameter using the PDO::PARAM_* constants.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function bindValue($param, $value, $type = null);

    /**
     * Binds a PHP variable to a corresponding named or question mark placeholder in the
     * SQL statement that was use to prepare the statement.
     *
     * @param mixed $column Parameter identifier.
     * @param mixed $variable Name of the PHP variable to bind to the SQL statement parameter.
     * @param integer|null $type Explicit data type for the parameter using the PDO::PARAM_* constants.
     * @param integer|null $length You must specify max length when using an OUT bind so that
     *                             PHP allocates enough memory to hold the returned value.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function bindParam($column, &$variable, $type = null, $length = null);

    /**
     * Fetches the SQLSTATE associated with the last operation on the statement handle.
     *
     * @return string The error code string.
     */
    public function errorCode();

    /**
     * Fetches extended error information associated with the last operation on the statement handle.
     *
     * @return array The error info array.
     */
    public function errorInfo();

    /**
     * Executes a prepared statement
     *
     * @param array|null $params An array of values with as many elements as there are
     *                           bound parameters in the SQL statement being executed.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function execute($params = null);

    /**
     * Returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     * executed by the corresponding object.
     *
     * @return integer The number of rows.
     */
    public function rowCount();
}
