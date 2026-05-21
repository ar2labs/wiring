<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Http\Exception\HttpException;
use Wiring\Interfaces\RouterInterface;

class RouterMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    private ?ResponseFactoryInterface $responseFactory;

    /**
     * Set the Router instance and optionally the response
     * factory to return the error responses.
     *
     * @param RouterInterface $router
     * @param ResponseFactoryInterface|null $responseFactory
     */
    public function __construct(
        RouterInterface $router,
        ?ResponseFactoryInterface $responseFactory = null
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
        try {
            return $this->router->dispatch($request);
        } catch (HttpException $exception) {
            if ($this->responseFactory === null) {
                throw $exception;
            }

            return $exception->buildResponse(
                $this->responseFactory->createResponse($exception->getStatusCode())
            );
        }
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

    public function getResponseFactory(): ?ResponseFactoryInterface
    {
        return $this->responseFactory;
    }
}
