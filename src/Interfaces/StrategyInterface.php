<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\RouteInterface;

interface StrategyInterface
{
    /**
     * Invoke the route callable based on the strategy.
     *
     * @param RouteInterface         $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function invokeRouteCallable(
        RouteInterface $route,
        ServerRequestInterface $request
    ): ResponseInterface;

    /**
     * Get a middleware that will decorate a NotFoundException.
     *
     * @param NotFoundException $exception
     *
     * @return MiddlewareInterface
     */
    public function getNotFoundDecorator(
        NotFoundException $exception
    ): MiddlewareInterface;

    /**
     * Get a middleware that will decorate a NotAllowedException.
     *
     * @param MethodNotAllowedException $exception
     *
     * @return MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(
        MethodNotAllowedException $exception
    ): MiddlewareInterface;

    /**
     * Get a middleware that will act as an exception handler.
     * The middleware must wrap the rest of the middleware
     * stack and catch any thrown exceptions.
     *
     * @return MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface;

    /**
     * Get a middleware that acts as a throwable handler,
     * it should wrap the rest of the middleware stack and
     * catch any throwables.
     *
     * @return MiddlewareInterface
     */
    public function getThrowableHandler(): MiddlewareInterface;
}
