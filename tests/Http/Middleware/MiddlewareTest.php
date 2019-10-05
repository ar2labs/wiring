<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Http\Middleware\EmitterMiddleware;
use Wiring\Http\Middleware\RouterMiddleware;
use Wiring\Interfaces\EmitterInterface;
use Wiring\Interfaces\RouterInterface;

final class MiddlewareTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testEmitterMiddleware()
    {
        $stream = $this->createStreamMock();
        $handler = $this->createRequestHandlerMock();

        $request = $this->createRequestMock();
        $request->method('getBody')
            ->willReturn($stream);

        $emitter = $this->createEmitterMock();

        $emitterMiddleware = new EmitterMiddleware($emitter);

        $this->assertInstanceOf(EmitterInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(MiddlewareInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->process($request, $handler)
        );

        // Without emitter param
        $emitterMiddleware = new EmitterMiddleware();

        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->process($request, $handler)
        );
    }

    /**
     * @throws \Exception
     */
    public function testRouteMiddleware()
    {
        $route = $this->createRouteMock();
        $responseFactory = $this->createResponseFactoryMock();

        $handler = $this->createRequestHandlerMock();
        $request = $this->createRequestMock();

        $routeMiddleware = new RouterMiddleware($route, $responseFactory);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $routeMiddleware->process($request, $handler)
        );

        $this->assertInstanceOf(
            RouterMiddleware::class,
            $routeMiddleware->responseFactory($responseFactory)
        );
    }

    private function createEmitterMock()
    {
        return $this->createMock(EmitterInterface::class);
    }

    private function createRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }

    private function createRequestHandlerMock()
    {
        return $this->createMock(RequestHandlerInterface::class);
    }

    private function createRouteMock()
    {
        return $this->createMock(RouterInterface::class);
    }

    private function createResponseFactoryMock()
    {
        return $this->createMock(ResponseFactoryInterface::class);
    }

    protected function header($string)
    {
        header($string);
    }
}
