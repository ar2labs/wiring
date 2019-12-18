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
use Wiring\Interfaces\ApplicationInterface;
use Wiring\Interfaces\ErrorHandlerInterface;

final class ApplicationTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
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
                ->willReturn(11);

            $response = $this->createResponseMock();
            $response->method('getBody')
                ->willReturn($stream);

            $app = new Application($container, $request, $response, true);
            $app->run();
            $this->assertInstanceOf(ResponseInterface::class, $app->handle($request));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertEquals('Bad Request', $e->getMessage());
        }

        try {
            // Test error handler callable
            $errorHandler = $this->createErrorHandlerMock();

            $container->method('has')
                ->with(ErrorHandlerInterface::class)
                ->willReturn(true);

            $container->method('get')
                ->with(ErrorHandlerInterface::class)
                ->willReturn($errorHandler);

            $app = new Application($container, $request, $response);
        } catch (\Throwable $e) {
            $this->assertEquals('Function name must be a string', $e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    /**
     * @return mixed
     */
    private function createServerRequestMock()
    {
        return $this->createMock(ServerRequestInterface::class);
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
    private function createMiddlewareMock()
    {
        return $this->createMock(MiddlewareInterface::class);
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
    private function createErrorHandlerMock()
    {
        return $this->createMock(ErrorHandlerInterface::class);
    }
}
