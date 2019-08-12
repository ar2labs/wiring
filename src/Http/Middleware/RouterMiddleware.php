<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseInterface dispatcher
     */
    private $dispatcher;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var string Attribute name for handler reference
     */
    private $attribute = 'request-handler';

    /**
     * Set the Dispatcher instance and optionally the response
     * factory to return the error responses.
     *
     * @param ResponseInterface $dispatcher
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(
        ResponseInterface $dispatcher,
        ResponseFactoryInterface $responseFactory = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->dispatcher;
    }

    /**
     * Set the response factory used.
     */
    public function responseFactory(
        ResponseFactoryInterface $responseFactory
    ): self {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ServerRequestInterface
     */
    protected function setHandler(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ServerRequestInterface {
        return $request->withAttribute($this->attribute, $handler);
    }
}
