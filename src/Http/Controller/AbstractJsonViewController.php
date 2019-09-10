<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Interfaces\RouteInterface;
use Wiring\Interfaces\ViewStrategyInterface;
use Throwable;

abstract class AbstractJsonViewController extends AbstractController
{
    /**
     * Create container and response interface.
     *
     * @param ContainerInterface $container
     * @param ResponseInterface $response
     */
    public function __construct(
        ContainerInterface $container,
        ResponseInterface $response
    ) {
        $this->setContainer($container);
        $this->setResponse($response);
    }

    /**
     * @return JsonStrategyInterface
     * @throws \Exception
     */
    public function json(): JsonStrategyInterface
    {
        return $this->get(JsonStrategyInterface::class);
    }

    /**
     * @return ViewStrategyInterface
     * @throws \Exception
     */
    public function view(): ViewStrategyInterface
    {
        return $this->get(ViewStrategyInterface::class);
    }

    /**
     * Invoke the route callable based on the abstract strategy.
     *
     * @param RouteInterface        $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function invokeRouteCallable(
        RouteInterface $route,
        ServerRequestInterface $request
    ): ResponseInterface {
        $controller = $route->getCallable($this->getContainer());
        $response = $controller($request, $route->getVars());

        return $this->applyDefaultResponseHeaders($response);
    }

    /**
     * Get a middleware that will decorate a NotFoundException.
     *
     * @param NotFoundException $exception
     *
     * @return MiddlewareInterface
     */
    public function getNotFoundDecorator(
        NotFoundException $exception
    ): MiddlewareInterface {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * Get a middleware that will decorate a NotAllowedException.
     *
     * @param MethodNotAllowedException $exception
     *
     * @return MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(
        MethodNotAllowedException $exception
    ): MiddlewareInterface {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * Return a middleware that simply throws an error
     *
     * @param Throwable $error
     *
     * @return MiddlewareInterface
     */
    protected function throwThrowableMiddleware(
        Throwable $error
    ): MiddlewareInterface {
        return new class($error) implements MiddlewareInterface {
            protected $error;

            public function __construct(Throwable $error)
            {
                $this->error = $error;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                throw $this->error;
            }
        };
    }

    /**
     * Get a middleware that will act as an exception handler.
     *
     * The middleware must wrap the rest of the middleware stack
     * and catch any thrown exceptions.
     *
     * @return MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->getThrowableHandler();
    }

    /**
     * Get a middleware that acts as a throwable handler, it should wrap the
     * rest of the middleware stack and catch any throwables.
     *
     * @return MiddlewareInterface
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        return new class implements MiddlewareInterface {
            /**
             * {@inheritdoc}
             *
             * @throws Throwable
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                try {
                    return $handler->handle($request);
                } catch (Throwable $e) {
                    error_log($e->getMessage());

                    throw $e;
                }
            }
        };
    }
}
