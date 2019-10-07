<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use Wiring\Strategy\JsonStrategy;
use Wiring\Interfaces\JsonStrategyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class JsonStrategyTest extends TestCase
{
    public function testRender()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->render(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategy::class, $result);
    }

    public function testWrite()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->write(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategyInterface::class, $result);
    }

    public function testTo()
    {
        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn('{"status": "ok"}');

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withStatus')
            ->with(200)
            ->willReturnSelf();

        $response->method('withHeader')
            ->with('Content-Type', 'application/json;charset=utf-8')
            ->willReturnSelf();

        $jsonStrategy = new JsonStrategy();
        $jsonStrategy->write('test');

        $this->assertInstanceOf(ResponseInterface ::class,
            $jsonStrategy->to($response));

        $jsonStrategy->render(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertInstanceOf(ResponseInterface ::class,
            $jsonStrategy->to($response));
    }

    private function createResponseMock()
    {
        return $this->createMock(ResponseInterface::class);
    }

    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }
}
