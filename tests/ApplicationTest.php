<?php

declare(strict_types=1);

namespace Wiring\Tests;

use Wiring\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Http\Exception\ErrorHandler;
use Wiring\Interfaces\ApplicationInterface;

final class ApplicationTest extends TestCase
{
    /**
     * @throws \BadMethodCallException
     */
    public function testInstanceCreated()
    {
        $container = $this->createContainerMock();
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();
        $middleware = $this->createMiddlewareMock();

        $app = new Application($container, $request, $response);

        $this->assertInstanceOf(Application::class, $app);
        $this->assertInstanceOf(ResponseInterface::class, $app->run());
        $this->assertInstanceOf(ResponseInterface::class, $app->stop());
        $this->assertInstanceOf(ApplicationInterface::class,
            $app->addMiddleware($middleware, 'test'));
        $this->assertInstanceOf(ApplicationInterface::class,
            $app->addAfterMiddleware($middleware, 'test'));
        $this->assertInstanceOf(MiddlewareInterface::class,
            $app->getMiddleware('test'));
        $this->assertInstanceOf(RequestHandlerInterface::class,
            $app->removeMiddleware('test'));
        $this->assertInstanceOf(RequestHandlerInterface::class,
            $app->addRouterMiddleware($middleware));
        $this->assertInstanceOf(RequestHandlerInterface::class,
            $app->addRequestHandler($middleware));
        $this->assertInstanceOf(RequestHandlerInterface::class,
            $app->addEmitterMiddleware($middleware));
        $this->assertIsBool($app->isAfterMiddleware());
        $this->assertNull($app->getMiddleware('test2'));
        $this->assertInstanceOf(ResponseInterface::class, $app->handle($request));

        try {
            // Test error handler
            $stream = $this->createStreamMock();
            $stream->method('write')
                ->with('Bad Request')
                ->willReturn(13);

            $response = $this->createResponseMock();
            $response->method('getBody')
                ->willReturn($stream);

            $app = new Application($container, $request, $response, true);

            $this->assertInstanceOf(ResponseInterface::class, $app->handle($request));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertEquals('Bad Request', $e->getMessage());
        }
    }

    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function createServerRequestMock()
    {
        return $this->createMock(ServerRequestInterface::class);
    }

    private function createResponseMock()
    {
        return $this->createMock(ResponseInterface::class);
    }

    private function createMiddlewareMock()
    {
        return $this->createMock(MiddlewareInterface::class);
    }

    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }

    private function createErrorHandlerMockCallback($request, $response, $exception)
    {
        return $this
            ->getMockBuilder(ErrorHandler::class)
            ->setConstructorArgs([$request, $response, $exception])
            ->setMethods(['__invoke'])
            ->getMock();
    }
}
