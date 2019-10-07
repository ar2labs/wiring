<?php

declare(strict_types=1);

namespace Wiring\Tests;

use Wiring\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Interfaces\ApplicationInterface;

final class ApplicationTest extends TestCase
{
    /**
     * @throws \Exception
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
}
