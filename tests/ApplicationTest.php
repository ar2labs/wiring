<?php

declare(strict_types=1);

namespace Wiring\Tests;

use ArrayObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use UnexpectedValueException;
use Wiring\Application;
use Wiring\Http\Exception\ErrorHandler;
use Wiring\Http\RequestHandler;
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
        $this->assertFalse($app->isFinished());
        $this->assertInstanceOf(ResponseInterface::class, $app->run());
        $this->assertTrue($app->isFinished());
        $this->assertInstanceOf(ResponseInterface::class, $app->stop());
        $this->assertInstanceOf(
            ApplicationInterface::class,
            $app->addMiddleware($middleware, 'test')
        );
        $this->assertInstanceOf(
            ApplicationInterface::class,
            $app->addAfterMiddleware($middleware, 'test')
        );
        $this->assertInstanceOf(
            MiddlewareInterface::class,
            $app->getMiddleware('test')
        );
        $this->assertInstanceOf(
            RequestHandlerInterface::class,
            $app->removeMiddleware('test')
        );
        $this->assertInstanceOf(
            RequestHandlerInterface::class,
            $app->addRouterMiddleware($middleware)
        );
        $this->assertInstanceOf(
            RequestHandlerInterface::class,
            $app->addRequestHandler($middleware)
        );
        $this->assertInstanceOf(
            RequestHandlerInterface::class,
            $app->addEmitterMiddleware($middleware)
        );
        $this->assertTrue($app->isAfterMiddleware());
        $this->assertNull($app->getMiddleware('test2'));
        $this->assertInstanceOf(ResponseInterface::class, $app->handle($request));

        try {
            // Test error handler
            $stream = $this->createStreamMock();
            $stream->method('write')
                ->willReturn(11);

            $response = $this->createResponseMock();
            $response->method('getBody')
                ->willReturn($stream);

            $app = new Application($container, $request, $response, true);
            $app->run();
            $this->assertInstanceOf(ResponseInterface::class, $app->handle($request));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertSame('Bad Request', $e->getMessage());
        }

        try {
            // Test error handler callable
            $errorHandler = $this->createErrorHandlerMock();
            $container = $this->createContainerMock();

            $container->method('has')
                ->willReturn(true);

            $container->method('get')
                ->willReturn($errorHandler);

            $app = new Application($container, $request, $response, true);
            $app->run();
            $this->fail('Expected non-callable error handler service to fail.');
        } catch (UnexpectedValueException $e) {
            $this->assertSame('Error handler service must be callable.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testRequestHandlerDestructorHandlesUnfinishedRequests()
    {
        $handler = new RequestHandler(
            $this->createContainerMock(),
            $this->createServerRequestMock(),
            $this->createResponseMock()
        );

        unset($handler);

        $this->addToAssertionCount(1);
    }

    /**
     * @return void
     */
    public function testAfterMiddlewareRunsOnlyAfterStop()
    {
        /** @var ArrayObject<int, string> $events */
        $events = new ArrayObject();
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();

        $app = new Application($this->createContainerMock(), $request, $response);
        $app->addMiddleware(new class ($events) implements MiddlewareInterface {
            /** @var ArrayObject<int, string> */
            private $events;

            /** @param ArrayObject<int, string> $events */
            public function __construct(ArrayObject $events)
            {
                $this->events = $events;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $this->events->append('normal-before');
                $response = $handler->handle($request);
                $this->events->append('normal-after');

                return $response;
            }
        });
        $app->addAfterMiddleware(new class ($events) implements MiddlewareInterface {
            /** @var ArrayObject<int, string> */
            private $events;

            /** @param ArrayObject<int, string> $events */
            public function __construct(ArrayObject $events)
            {
                $this->events = $events;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $this->events->append('after');

                return $handler->handle($request);
            }
        }, 'after');

        $this->assertSame($response, $app->run());
        $this->assertSame(['normal-before', 'normal-after'], $events->getArrayCopy());

        $this->assertSame($response, $app->stop());
        $this->assertSame(['normal-before', 'normal-after', 'after'], $events->getArrayCopy());
    }

    /**
     * @return void
     */
    public function testRequestHandlerDestructorDoesNotRunPipelineImplicitly()
    {
        /** @var ArrayObject<int, string> $events */
        $events = new ArrayObject();
        $handler = new RequestHandler(
            $this->createContainerMock(),
            $this->createServerRequestMock(),
            $this->createResponseMock()
        );
        $handler->addMiddleware(new class ($events) implements MiddlewareInterface {
            /** @var ArrayObject<int, string> */
            private $events;

            /** @param ArrayObject<int, string> $events */
            public function __construct(ArrayObject $events)
            {
                $this->events = $events;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $this->events->append('normal');

                return $handler->handle($request);
            }
        });

        unset($handler);

        $this->assertSame([], $events->getArrayCopy());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testCallableErrorHandlerMustReturnResponse()
    {
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(true);
        $container->method('get')
            ->willReturn(static fn (
                ServerRequestInterface $request,
                ResponseInterface $response,
                Throwable $error
            ): null => null);

        $app = new Application(
            $container,
            $this->createServerRequestMock(),
            $this->createResponseMock(),
            true
        );

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Error handler service must return a response.');

        $app->run();
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testCallableErrorHandlerCanReturnResponse()
    {
        $response = $this->createResponseMock();
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(true);
        $container->method('get')
            ->willReturn(static fn (
                ServerRequestInterface $request,
                ResponseInterface $response,
                Throwable $error
            ): ResponseInterface => $response);

        $app = new Application(
            $container,
            $this->createServerRequestMock(),
            $response,
            true
        );

        $this->assertSame($response, $app->run());
    }

    /**
     * @return void
     */
    public function testFallbackErrorHandlerUsesGenericMessage()
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with(ErrorHandler::DEFAULT_MESSAGE)
            ->willReturn(strlen(ErrorHandler::DEFAULT_MESSAGE));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);
        $response->expects($this->once())
            ->method('withStatus')
            ->with(500)
            ->willReturnSelf();

        $app = new Application(
            $this->createContainerMock(),
            $this->createServerRequestMock(),
            $response,
            true
        );

        $this->assertSame($response, $app->run());
    }

    private function createContainerMock(): ContainerInterface&Stub
    {
        return $this->createStub(ContainerInterface::class);
    }

    private function createServerRequestMock(): ServerRequestInterface&Stub
    {
        return $this->createStub(ServerRequestInterface::class);
    }

    private function createResponseMock(): ResponseInterface&Stub
    {
        return $this->createStub(ResponseInterface::class);
    }

    private function createMiddlewareMock(): MiddlewareInterface&Stub
    {
        return $this->createStub(MiddlewareInterface::class);
    }

    private function createStreamMock(): StreamInterface&Stub
    {
        return $this->createStub(StreamInterface::class);
    }

    private function createErrorHandlerMock(): ErrorHandlerInterface&Stub
    {
        return $this->createStub(ErrorHandlerInterface::class);
    }
}
