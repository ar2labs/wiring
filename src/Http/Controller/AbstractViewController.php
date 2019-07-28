<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use League\Route\Route;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\MiddlewareInterface;
use Wiring\Interfaces\ViewStrategyInterface;
use Wiring\Http\Exception\{MethodNotAllowedException, NotFoundException};
use Throwable;

abstract class AbstractViewController extends AbstractController
{
    /**
     * Create container and response interface.
     *
     * @param ContainerInterface $container
     * @param ResponseFactoryInterface $response
     */
    public function __construct(ContainerInterface $container, ResponseInterface $response)
    {
        $this->setContainer($container);
        $this->setResponse($response);
    }

    /**
     * Get View renderer.
     *
     * @return ViewStrategyInterface
     */
    public function view(): ViewStrategyInterface
    {
        return $this->get(ViewStrategyInterface::class);
    }

    /**
     * Invoke the route callable based on the strategy.
     *
     * @param Route                  $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface$request): ResponseInterface
    {
        $controller = $route->getCallable($this->getContainer());

        $response = $controller($request, $route->getVars());
        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
    }

    /**
     * Get a middleware that will decorate a NotFoundException.
     *
     * @param NotFoundException $exception
     *
     * @return MiddlewareInterface
     */
    public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface
    {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * Get a middleware that will decorate a NotAllowedException.
     *
     * @param MethodNotAllowedException $exception
     *
     * @return MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface
    {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * Return a middleware that simply throws an error
     *
     * @param \Throwable $error
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    protected function throwThrowableMiddleware(Throwable $error): MiddlewareInterface
    {
        return new class($error) implements MiddlewareInterface
        {
            protected $error;

            public function __construct(Throwable $error)
            {
                $this->error = $error;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ?ResponseInterface {
                throw $this->error;
            }
        };
    }

    /**
     * Get a middleware that will act as an exception handler.
     *
     * The middleware must wrap the rest of the middleware stack and catch any
     * thrown exceptions.
     *
     * @return MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->getThrowableHandler();
    }

    /**
     * Get a middleware that acts as a throwable handler, it should wrap the rest of the
     * middleware stack and catch any throwables.
     *
     * @return MiddlewareInterface
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        return new class implements MiddlewareInterface
        {
            /**
             * {@inheritdoc}
             *
             * @throws Throwable
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                try {
                    return $requestHandler->handle($request);
                } catch (Throwable $e) {
                    throw $e;
                }
            }
        };
    }
}
