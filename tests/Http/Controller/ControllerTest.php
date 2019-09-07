<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Wiring\Http\Controller\AbstractController;
use Wiring\Http\Controller\AbstractJsonController;
use Wiring\Http\Controller\AbstractViewController;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Interfaces\ViewStrategyInterface;

final class ControllerTest extends TestCase
{
    /**
     * @throws \Exception
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

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractViewController::class, $controller);
        $this->assertInstanceOf(ViewStrategyInterface::class, $controller->view());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
    }

    /**
     * @throws \Exception
     */
    public function testJsonController()
    {
        $container = $this->createContainerMock();

        $view = $this->createJsonStrategyMock();

        $container->method('get')
            ->with(JsonStrategyInterface::class)
            ->willReturn($view);

        $response = $this->createResponseMock();
        $response->method('withStatus')
            ->willReturnSelf();

        $controller = new SimpleJsonController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractJsonController::class, $controller);
        $this->assertInstanceOf(JsonStrategyInterface::class, $controller->json());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
    }

    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function createViewStrategyMock()
    {
        return $this->createMock(ViewStrategyInterface::class);
    }

    private function createResponseMock()
    {
        return $this->createMock(ResponseInterface::class);
    }

    private function createJsonStrategyMock()
    {
        return $this->createMock(JsonStrategyInterface::class);
    }
}
