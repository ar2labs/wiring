<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Exception;
use Wiring\Http\Exception\HttpException;

class BadRequestException extends HttpException
{
    /**
     * Constructor
     *
     * @param string    $message
     * @param Exception $previous
     * @param int $code
     */
    public function __construct(
        string $message = 'Bad Request',
        ?Exception $previous = null,
        int $code = 0
    ) {
        parent::__construct(400, $message, $previous, [], $code);
    }
}
