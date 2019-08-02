<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Exception;

class UnauthorizedException extends HttpException
{
    /**
     * Constructor
     *
     * @param string    $message
     * @param Exception $previous
     * @param int $code
     */
    public function __construct(
        string $message = 'Unauthorized',
        ?Exception $previous = null,
        int $code = 0
    ) {
        parent::__construct(401, $message, $previous, [], $code);
    }
}
