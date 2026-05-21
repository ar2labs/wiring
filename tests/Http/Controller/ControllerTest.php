<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Controller;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionProperty;
use stdClass;
use UnexpectedValueException;
use Wiring\Http\Controller\AbstractController;
use Wiring\Http\Controller\AbstractJsonController;
use Wiring\Http\Controller\AbstractJsonViewController;
use Wiring\Http\Controller\AbstractRestfulController;
use Wiring\Http\Controller\AbstractViewController;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Interfaces\RouteInterface;
use Wiring\Interfaces\ViewStrategyInterface;

final class ControllerTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testSimpleController()
    {
        $container = $this->createContainerMock();

        $view = $this->createViewStrategyMock();

        $container->method('get')
            ->willReturn($view);

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();

        $controller = new SimpleMockController($container, $response);

        $notFound = new NotFoundException();

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractViewController::class, $controller);
        $this->assertInstanceOf(ViewStrategyInterface::class, $controller->view());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
        $this->assertInstanceOf(MiddlewareInterface::class, $controller->getNotFoundDecorator($notFound));

        try {
            $request = $this->createRequestMock();
            $requestHandler = $this->createRequestHandlerMock();

            $notAllowed = new MethodNotAllowedException();

            $this->assertInstanceOf(
                MiddlewareInterface::class,
                $controller->getMethodNotAllowedDecorator($notAllowed)
                    ->process($request, $requestHandler)
            );
        } catch (MethodNotAllowedException $e) {
            $this->assertInstanceOf(MethodNotAllowedException::class, $e);
            $this->assertSame('Method Not Allowed', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testBaseControllerRouteCallableRedirectsAndThrowableHandler()
    {
        $container = $this->createContainerMock();
        $response = $this->createResponseMock();
        $response->method('hasHeader')
            ->willReturn(false);
        $response->method('withHeader')
            ->willReturnSelf();
        $response->method('withStatus')
            ->willReturnSelf();

        $controller = new SimpleMockController($container, $response);

        $route = $this->createRouteMock();
        $route->method('getVars')
            ->willReturn(['id' => 10]);
        $route->method('getCallable')
            ->willReturn(static fn (ServerRequestInterface $request, array $vars): ResponseInterface => $response);

        $request = $this->createRequestMock();

        $this->assertSame($response, $controller->invokeRouteCallable($route, $request));
        $this->assertSame($response, $controller->redirect($response, '/next'));
        $this->assertSame($response, $controller->redirect($response, '/next', null));

        $handler = $this->createRequestHandlerMock();
        $handler->method('handle')
            ->willReturn($response);

        $this->assertSame($response, $controller->getExceptionHandler()->process($request, $handler));

        $throwingHandler = $this->createRequestHandlerMock();
        $throwingHandler->method('handle')
            ->willThrowException(new Exception('base handler failure'));

        $errorLog = ini_set('error_log', sys_get_temp_dir() . '/wiring-controller-error.log');

        try {
            $controller->getThrowableHandler()->process($request, $throwingHandler);
            $this->fail('Expected throwable handler to rethrow the handler exception.');
        } catch (Exception $e) {
            $this->assertSame('base handler failure', $e->getMessage());
        } finally {
            if (is_string($errorLog)) {
                ini_set('error_log', $errorLog);
            }
        }
    }

    /**
     * @return void
     */
    public function testRedirectRejectsUnsafeUrls()
    {
        $controller = new SimpleMockController($this->createContainerMock(), $this->createResponseMock());
        $response = $this->createResponseMock();

        foreach ([
            '',
            ' https://example.com',
            'https://example.com/path',
            '//example.com/path',
            'javascript:alert(1)',
            '/safe%0d%0aX-Injected:%20yes',
            '/safe' . "\r\n" . 'X-Injected: yes',
            '\\example.com\\path',
        ] as $url) {
            try {
                $controller->redirect($response, $url);
                $this->fail(sprintf('Expected redirect URL "%s" to be rejected.', $url));
            } catch (InvalidArgumentException $e) {
                $this->assertStringStartsWith('Redirect URL must', $e->getMessage());
            }
        }
    }

    /**
     * @return void
     */
    public function testBaseControllerRequiresRouteCallableResponse()
    {
        $controller = new SimpleMockController($this->createContainerMock(), $this->createResponseMock());
        $route = $this->createRouteMock();
        $route->method('getVars')
            ->willReturn([]);
        $route->method('getCallable')
            ->willReturn(static fn (): string => 'not a response');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Route callable must return a response.');

        $controller->invokeRouteCallable($route, $this->createRequestMock());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testJsonViewController()
    {
        $view = $this->createViewStrategyMock();

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($view);

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();

        $controller = new SimpleJsonViewController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonViewController::class, $controller);
        $this->assertInstanceOf(ViewStrategyInterface::class, $controller->view());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());

        $json = $this->createJsonStrategyMock();

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($json);

        $controller = new SimpleJsonViewController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonViewController::class, $controller);
        $this->assertInstanceOf(JsonStrategyInterface::class, $controller->json());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
    }

    /**
     * @return void
     */
    public function testJsonViewControllerValidatesStrategies()
    {
        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn(new stdClass());

        $controller = new SimpleJsonViewController($container, $this->createResponseMock());

        try {
            $controller->json();
            $this->fail('Expected an invalid JSON strategy service to fail.');
        } catch (UnexpectedValueException $e) {
            $this->assertSame('JSON strategy interface not implemented.', $e->getMessage());
        }

        try {
            $controller->view();
            $this->fail('Expected an invalid view strategy service to fail.');
        } catch (UnexpectedValueException $e) {
            $this->assertSame('View strategy interface not implemented.', $e->getMessage());
        }

        $viewController = new SimpleMockController($container, $this->createResponseMock());

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('View strategy interface not implemented.');

        $viewController->view();
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testJsonController()
    {
        $container = $this->createContainerMock();

        $json = $this->createJsonStrategyMock();

        $container->method('get')
            ->willReturn($json);

        $request = $this->createRequestMock();

        $handler = $this->createRequestHandlerMock();

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();
        $response->method('withHeader')
            ->willReturnSelf();

        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn(80);

        $response->method('getBody')
            ->willReturn($stream);

        $controller = new SimpleJsonController($container, $response);

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonController::class, $controller);
        $this->assertInstanceOf(JsonStrategyInterface::class, $controller->json());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
        $this->assertInstanceOf(MiddlewareInterface::class, $controller->getThrowableHandler());
        $this->assertInstanceOf(ResponseInterface::class, $controller->getThrowableHandler()->process($request, $handler));
        $this->assertInstanceOf(SimpleJsonController::class, $controller->addDefaultResponseHeaders($headers));
        $this->assertSame(['content-type' => 'application/json'], $controller->getDefaultResponseHeaders());

        $handler->method('handle')
            ->willThrowException(new Exception('Throwable test'));

        $this->assertInstanceOf(ResponseInterface::class, $controller->getThrowableHandler()->process($request, $handler));

        $response->method('hasHeader')
            ->willReturn(false);

        $response->method('withHeader')
            ->willReturnSelf();

        $this->assertInstanceOf(ResponseInterface::class, $controller->addDefaultResponse($response));
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testJsonControllerInvokesJsonAndResponseRouteResults()
    {
        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($this->createJsonStrategyMock());

        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn(11);

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);
        $response->method('hasHeader')
            ->willReturn(false);
        $response->method('withHeader')
            ->willReturnSelf();

        $controller = new SimpleJsonController($container, $response);
        $request = $this->createRequestMock();

        $jsonRoute = $this->createRouteMock();
        $jsonRoute->method('getVars')
            ->willReturn([]);
        $jsonRoute->method('getCallable')
            ->willReturn(static fn (): array => ['ok' => true]);

        $this->assertSame($response, $controller->invokeRouteCallable($jsonRoute, $request));

        $responseRoute = $this->createRouteMock();
        $responseRoute->method('getVars')
            ->willReturn([]);
        $responseRoute->method('getCallable')
            ->willReturn(static fn (): ResponseInterface => $response);

        $this->assertSame($response, $controller->invokeRouteCallable($responseRoute, $request));
    }

    /**
     * @return void
     */
    public function testJsonControllerRejectsInvalidRouteResults()
    {
        $controller = new SimpleJsonController($this->createContainerMock(), $this->createResponseMock());
        $route = $this->createRouteMock();
        $route->method('getVars')
            ->willReturn([]);
        $route->method('getCallable')
            ->willReturn(static fn (): string => 'not json');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Route callable must return a response.');

        $controller->invokeRouteCallable($route, $this->createRequestMock());
    }

    /**
     * @return void
     */
    public function testJsonControllerRejectsUnencodableRouteResults()
    {
        $resource = fopen('phpunit.xml.dist', 'r');
        $this->assertIsResource($resource);

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($this->createStreamMock());

        $controller = new SimpleJsonController($this->createContainerMock(), $response);
        $route = $this->createRouteMock();
        $route->method('getVars')
            ->willReturn([]);
        $route->method('getCallable')
            ->willReturn(static fn (): array => ['resource' => $resource]);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Route callable result must be JSON encodable.');

        try {
            $controller->invokeRouteCallable($route, $this->createRequestMock());
        } finally {
            fclose($resource);
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testJsonControllerExceptionDecoratorsBuildJsonResponses()
    {
        $response = $this->createResponseMock();
        $response->method('withAddedHeader')
            ->willReturnSelf();
        $response->method('withStatus')
            ->willReturnSelf();

        $stream = $this->createStreamMock();
        $stream->method('isWritable')
            ->willReturn(true);
        $stream->method('write')
            ->willReturn(62);

        $response->method('getBody')
            ->willReturn($stream);

        $controller = new SimpleJsonController($this->createContainerMock(), $response);
        $request = $this->createRequestMock();
        $handler = $this->createRequestHandlerMock();

        $this->assertSame(
            $response,
            $controller->getNotFoundDecorator(new NotFoundException())->process($request, $handler)
        );

        $this->assertSame(
            $response,
            $controller->getMethodNotAllowedDecorator(new MethodNotAllowedException())->process($request, $handler)
        );
    }

    /**
     * @return void
     */
    public function testJsonControllerValidatesJsonStrategyAndContainer()
    {
        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn(new stdClass());

        $controller = new SimpleJsonController($container, $this->createResponseMock());

        try {
            $controller->json();
            $this->fail('Expected an invalid JSON strategy service to fail.');
        } catch (UnexpectedValueException $e) {
            $this->assertSame('JSON strategy interface not implemented.', $e->getMessage());
        }

        $containerProperty = new ReflectionProperty($controller, 'container');
        $containerProperty->setValue($controller, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Container instance error');

        $controller->getThrowableHandler();
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testRestfulController()
    {
        $request = $this->createRequestMock();

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();

        $response->method('getReasonPhrase')
            ->willReturn('');

        $json = $this->createJsonStrategyMock();
        $json->method('render')
            ->willReturnSelf();

        $json->method('write')
            ->willReturnSelf();

        $json->method('to')
            ->willReturn($response);

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($json);

        $controller = new SimpleRestfulController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonController::class, $controller);
        $this->assertInstanceOf(AbstractRestfulController::class, $controller);
        $this->assertInstanceOf(JsonStrategyInterface::class, $controller->json());
        $this->assertInstanceOf(ResponseInterface::class, $controller->index($request));
        $this->assertInstanceOf(ResponseInterface::class, $controller->create($request));
        $this->assertInstanceOf(ResponseInterface::class, $controller->read($request, []));
        $this->assertInstanceOf(ResponseInterface::class, $controller->update($request, []));
        $this->assertInstanceOf(ResponseInterface::class, $controller->delete($request, []));
        $this->assertSame('info', $controller->info()['status']);
        $this->assertSame('success', $controller->success()['status']);
        $this->assertSame('error', $controller->error()['status']);
        $this->assertSame('fail', $controller->fail()['status']);
    }

    private function createContainerMock(): ContainerInterface&Stub
    {
        return $this->createStub(ContainerInterface::class);
    }

    private function createViewStrategyMock(): ViewStrategyInterface&Stub
    {
        return $this->createStub(ViewStrategyInterface::class);
    }

    private function createRequestMock(): ServerRequestInterface&Stub
    {
        return $this->createStub(ServerRequestInterface::class);
    }

    private function createRequestHandlerMock(): RequestHandlerInterface&Stub
    {
        return $this->createStub(RequestHandlerInterface::class);
    }

    private function createResponseMock(): ResponseInterface&Stub
    {
        return $this->createStub(ResponseInterface::class);
    }

    private function createJsonStrategyMock(): JsonStrategyInterface&Stub
    {
        return $this->createStub(JsonStrategyInterface::class);
    }

    private function createRouteMock(): RouteInterface&Stub
    {
        return $this->createStub(RouteInterface::class);
    }

    private function createStreamMock(): StreamInterface&Stub
    {
        return $this->createStub(StreamInterface::class);
    }
}
