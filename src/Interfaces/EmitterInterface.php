<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    /**
     * Emitter for PSR-7 response.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function emit(ResponseInterface $response): ResponseInterface;
}
