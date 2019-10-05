<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wiring\Interfaces\RouterInterface;

class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ResponseFactoryInterface|null
     */
    private $responseFactory;

    /**
     * Set the Router instance and optionally the response
     * factory to return the error responses.
     *
     * @param RouterInterface $router
     * @param ResponseFactoryInterface|null $responseFactory
     */
    public function __construct(
        RouterInterface $router,
        ResponseFactoryInterface $responseFactory = null
    ) {
        $this->router = $router;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process a dispatch request and return a response.
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->router->dispatch($request);
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
}
