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
     * @var Route Route dispatcher
     */
    private $router;

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
     * @param Router $router
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(
        $router,
        ResponseFactoryInterface $responseFactory = null
    ) {
        $this->router = $router;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process a server request and return a response.
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

    /**
     * Get the router.
     *
     * @return mixed
     */
    public function getRouter()
    {
        return $this->route;
    }

    /**
     * Set the router.
     *
     * @param mixed $route
     */
    public function setRouter($route)
    {
        $this->route = $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @param $handler
     * @return ServerRequestInterface
     */
    protected function setHandler(
        ServerRequestInterface $request,
        $handler
    ): ServerRequestInterface {
        return $request->withAttribute($this->attribute, $handler);
    }
}
