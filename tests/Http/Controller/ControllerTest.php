<?php

namespace Wiring\Tests\Http\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Wiring\Http\Controller\AbstractController;
use Wiring\Http\Controller\AbstractViewController;
use Wiring\Interfaces\ViewStrategyInterface;

class SimpleMockController extends AbstractViewController
{
    public function indexAction(): ResponseInterface
    {
        return $this->response;
    }
}

final class ControllerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testSimpleController()
    {
        $container = $this->createMock(ContainerInterface::class);
        $view = $this->createMock(ViewStrategyInterface::class);
        $container->method('get')
            ->with(ViewStrategyInterface::class)
            ->willReturn($view);
        $response = $this->createMock(ResponseInterface::class);
        $response->method('withStatus')
            ->willReturnSelf();

        $controller = new SimpleMockController($container, $response);

        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(AbstractViewController::class, $controller);
        $this->assertInstanceOf(ViewStrategyInterface::class, $controller->view());
        $this->assertInstanceOf(ResponseInterface::class, $controller->indexAction());
    }
}
