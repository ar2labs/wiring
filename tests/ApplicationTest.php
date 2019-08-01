<?php
declare(strict_types=1);

use Wiring\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ApplicationTest extends TestCase
{
    public function testInstanceCreated()
    {
        $container = $this->createMock(ContainerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $app = new Application($container, $request, $response);

        $this->assertInstanceOf(Application::class, $app);
    }
}
