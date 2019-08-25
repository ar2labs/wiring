<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface JsonStrategyInterface
{
    /**
     * Write data with JSON encode.
     *
     * @param mixed $data            The data array or object
     * @param int   $encodingOptions JSON encoding options
     *
     * @return self
     */
    public function render($data, int $encodingOptions = 0);

    /**
     * Write JSON to data response.
     *
     * @param mixed $data The data
     *
     * @return self
     */
    public function write($data);

    /**
     * Return response with JSON header and status.
     *
     * @param ResponseInterface|null $response
     * @param int                    $status
     *
     * @return ResponseInterface
     */
    public function to(
        ?ResponseInterface $response,
        int $status = 200
    ): ResponseInterface;
}
