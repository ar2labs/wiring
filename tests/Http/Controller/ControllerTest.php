<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Controller;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Http\Controller\AbstractController;
use Wiring\Http\Controller\AbstractJsonController;
use Wiring\Http\Controller\AbstractJsonViewController;
use Wiring\Http\Controller\AbstractRestfulController;
use Wiring\Http\Controller\AbstractViewController;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\JsonStrategyInterface;
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
            ->with(ViewStrategyInterface::class)
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
            $this->assertEquals('Method Not Allowed', $e->getMessage());
        }
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
            ->with(ViewStrategyInterface::class)
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
            ->with(JsonStrategyInterface::class)
            ->willReturn($json);

        $controller = new SimpleJsonViewController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonViewController::class, $controller);
        $this->assertInstanceOf(JsonStrategyInterface::class, $controller->json());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
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
            ->with(JsonStrategyInterface::class)
            ->willReturn($json);

        $request = $this->createRequestMock();

        $handler = $this->createRequestHandlerMock();

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();

        $stream = $this->createStreamMock();
        $stream->method('write')
            ->with('{"code":500,"status":"error","message":"<span>Throwable test<\/span>","data":[]}')
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
        $this->assertIsArray($controller->getDefaultResponseHeaders());

        $handler->method('handle')
            ->willThrowException(new Exception('Throwable test'));

        $this->assertInstanceOf(ResponseInterface::class, $controller->getThrowableHandler()->process($request, $handler));

        $response->method('hasHeader')
            ->with('content-type')
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
            ->with(JsonStrategyInterface::class)
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
        $this->assertIsArray($controller->info());
        $this->assertIsArray($controller->success());
        $this->assertIsArray($controller->error());
        $this->assertIsArray($controller->fail());
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
    private function createViewStrategyMock()
    {
        return $this->createMock(ViewStrategyInterface::class);
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
    private function createRequestHandlerMock()
    {
        return $this->createMock(RequestHandlerInterface::class);
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
    private function createJsonStrategyMock()
    {
        return $this->getMockBuilder(JsonStrategyInterface::class)
            ->setMethods([
                'render',
                'write',
                'to',
            ])
            ->getMock();
    }

    /**
     * @return mixed
     */
    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }
}
