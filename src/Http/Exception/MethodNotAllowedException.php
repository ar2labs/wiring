<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Exception;

class MethodNotAllowedException extends HttpException
{
    /**
     * Constructor
     *
     * @param array     $allowed
     * @param string    $message
     * @param Exception $previous
     * @param int $code
     */
    public function __construct(
        array      $allowed = [],
        string     $message = 'Method Not Allowed',
        ?Exception $previous = null,
        int        $code = 0
    ) {
        $headers = [
            'Allow' => implode(', ', $allowed),
        ];

        parent::__construct(405, $message, $previous, $headers, $code);
    }
}
