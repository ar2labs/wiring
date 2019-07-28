<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class EmitterMiddleware implements MiddlewareInterface
{
    /**
     * @var EmitterInterface
     */
    protected $emitter;

    /**
     * @param EmitterInterface $emitter
     */
    public function __construct(EmitterInterface $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * Process an session request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Checks if or where headers not have been sent
        if (headers_sent() === false) {
            // Execute code before calling the next middleware
            $this->emitter->emit($response);
        }

        return $response;
    }
}
