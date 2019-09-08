<?php

declare(strict_types=1);

namespace Wiring\Tests;

use Wiring\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ApplicationTest extends TestCase
{
    public function testInstanceCreated()
    {
        $container = $this->createContainerMock();
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();

        $app = new Application($container, $request, $response);

        $this->assertInstanceOf(Application::class, $app);
        $this->assertInstanceOf(ResponseInterface::class, $app->run());
        $this->assertInstanceOf(ResponseInterface::class, $app->stop());
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
}
