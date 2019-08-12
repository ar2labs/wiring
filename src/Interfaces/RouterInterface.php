<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * Dispatches against the provided HTTP method and URI.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface;
}
