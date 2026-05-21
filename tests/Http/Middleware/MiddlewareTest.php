<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Middleware;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
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
        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->emit($response)
        );

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
        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->emit($response)
        );

        // Content-Length, protocol, and reason-phrase fallbacks
        $response = $this->createResponseMock();
        $response->method('getHeaders')
            ->willReturn(['X-Test' => ['1']]);
        $response->method('getHeaderLine')
            ->willReturn('4');
        $response->method('getProtocolVersion')
            ->willReturn('');
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getReasonPhrase')
            ->willReturn('');

        $stream = $this->createStreamMock();
        $stream->method('isSeekable')
            ->willReturn(false);
        $stream->method('eof')
            ->willReturnOnConsecutiveCalls(false, true, true);
        $stream->method('read')
            ->willReturn('test');

        $response->method('getBody')
            ->willReturn($stream);

        ob_start();

        $this->assertInstanceOf(
            ResponseInterface::class,
            $emitterMiddleware->emit($response)
        );

        ob_end_clean();

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
     * @return void
     */
    public function testEmitterRejectsHeadersWithLineBreaks()
    {
        $response = $this->createResponseMock();
        $response->method('getHeaders')
            ->willReturn(['X-Test' => ["ok\r\nX-Injected: yes"]]);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Header values must not contain line breaks.');

        (new EmitterMiddleware())->emit($response);
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
        $this->assertSame($responseFactory, $routeMiddleware->getResponseFactory());
    }

    private function createEmitterMock(): EmitterInterface&Stub
    {
        return $this->createStub(EmitterInterface::class);
    }

    private function createRequestMock(): ServerRequestInterface&Stub
    {
        return $this->createStub(ServerRequestInterface::class);
    }

    private function createResponseMock(): ResponseInterface&Stub
    {
        return $this->createStub(ResponseInterface::class);
    }

    private function createStreamMock(): StreamInterface&Stub
    {
        return $this->createStub(StreamInterface::class);
    }

    private function createRequestHandlerMock(): RequestHandlerInterface&Stub
    {
        return $this->createStub(RequestHandlerInterface::class);
    }

    private function createRouteMock(): RouterInterface&Stub
    {
        return $this->createStub(RouterInterface::class);
    }

    private function createResponseFactoryMock(): ResponseFactoryInterface&Stub
    {
        return $this->createStub(ResponseFactoryInterface::class);
    }

    protected function header(string $header): void
    {
        header($header);
    }
}
