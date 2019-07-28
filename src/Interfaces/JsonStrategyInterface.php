<?php

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface JsonStrategyInterface
{
    /**
     * Write data with JSON encode.
     *
     * @param array $data The data
     * @param int $encodingOptions JSON encoding options
     *
     * @return self
     */
    public function render($data, $encodingOptions = 0);

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
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param int $status
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function to(ResponseInterface $response, $status = 200): ResponseInterface;
}
