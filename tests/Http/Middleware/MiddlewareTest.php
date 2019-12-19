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
     *
     * @return void
     */
    public function testEmitterMiddleware()
    {
        $stream = $this->createStreamMock();
        $stream->method('write')
            ->with('test')
            ->willReturn(4);

        $request = $this->createRequestMock();
        $request->method('getBody')
            ->willReturn($stream);

        $emitter = $this->createEmitterMock();

        $emitterMiddleware = new EmitterMiddleware($emitter);

        $handler = $this->createRequestHandlerMock();

        $this->assertInstanceOf(EmitterInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(MiddlewareInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->process($request, $handler)
        );

        $response = $this->createResponseMock();
        $response->method('getHeaders')
            ->willReturn(['Set-Cookie' => ['0']]);

        $response->method('getProtocolVersion')
            ->willReturn('1.1');

        $response->method('getStatusCode')
            ->willReturn(200);

        $response->method('getReasonPhrase')
            ->willReturn('OK');

        $stream = $this->createStreamMock();
        $stream->method('isSeekable')
            ->willReturn(true);

        $response->method('getBody')
            ->willReturn($stream);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(ResponseInterface::class,
            $emitterMiddleware->emit($response));

        // Status Code != 200
        $response = $this->createResponseMock();
        $response->method('getHeaders')
            ->willReturn(['Set-Cookie' => ['0']]);

        $response->method('getProtocolVersion')
            ->willReturn('1.1');

        $response->method('getStatusCode')
            ->willReturn(401);

        $response->method('getReasonPhrase')
            ->willReturn('OK');

        $stream = $this->createStreamMock();
        $stream->method('isSeekable')
            ->willReturn(true);

        $response->method('getBody')
            ->willReturn($stream);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(ResponseInterface::class,
                $emitterMiddleware->emit($response));

        // Without emitter
        $emitterMiddleware = new EmitterMiddleware();

        $handler = $this->createRequestHandlerMock();
        $handler->method('handle')
            ->willReturn($response);

        $this->assertInstanceOf(EmitterInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(MiddlewareInterface::class, $emitterMiddleware);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->process($request, $handler)
        );
    }

    /**
     * @throws \Exception
     *
     * @return void
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

    /**
     * @return mixed
     */
    private function createEmitterMock()
    {
        return $this->createMock(EmitterInterface::class);
    }

    /**
     * @return mixed
     */
    private function createRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    /**
     * @return mixed
     */
    private function createResponseMock()
    {
        return $this->createMock(ResponseInterface::class);
    }

    /**
     * @return mixed
     */
    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }

    /**
     * @return mixed
     */
    private function createRequestHandlerMock()
    {
        return $this->createMock(RequestHandlerInterface::class);
    }

    /**
     * @return mixed
     */
    private function createRouteMock()
    {
        return $this->createMock(RouterInterface::class);
    }

    /**
     * @return mixed
     */
    private function createResponseFactoryMock()
    {
        return $this->createMock(ResponseFactoryInterface::class);
    }

    /**
     * @param string $header
     *
     * @return mixed
     */
    protected function header($header)
    {
        header($header);
    }
}
