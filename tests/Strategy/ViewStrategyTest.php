<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use Wiring\Strategy\ViewStrategy;
use Wiring\Interfaces\ViewStrategyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ViewStrategyTest extends TestCase
{
    public function testRender()
    {
        $viewStrategy = new ViewStrategy('test');
        $result = $viewStrategy->render('test', ['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(ViewStrategyInterface::class, $result);
    }

    public function testWrite()
    {
        $viewStrategy = new ViewStrategy('test');
        $result = $viewStrategy->write('test');

        $this->assertInstanceOf(ViewStrategyInterface::class, $result);
    }

    public function testEngine()
    {
        $engine = $this->createViewStrategyMock();
        $viewStrategy = new ViewStrategy($engine);
        $result = $viewStrategy->engine();

        $this->assertEquals($engine, $result);
    }

    public function testTo()
    {
        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn('test');

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withStatus')
            ->with(200)
            ->willReturnSelf();

        $engine = $this->createViewStrategyMock();

        $viewStrategy = new ViewStrategy($engine);
        $viewStrategy->write('test');

        $this->assertInstanceOf(ResponseInterface::class,
            $viewStrategy->to($response));

        $engine->method('render')
            ->willReturn('test');

        $viewStrategy->render('test');

        $this->assertInstanceOf(ResponseInterface::class,
            $viewStrategy->to($response));
    }

    private function createViewStrategyMock()
    {
        return $this->createMock(ViewStrategyInterface::class);
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
