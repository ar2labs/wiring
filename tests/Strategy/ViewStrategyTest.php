<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;
use UnexpectedValueException;
use Wiring\Interfaces\ViewStrategyInterface;
use Wiring\Strategy\ViewStrategy;

final class ViewStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testRender()
    {
        $viewStrategy = new ViewStrategy('test');
        $result = $viewStrategy->render('test', ['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(ViewStrategyInterface::class, $result);
    }

    /**
     * @return void
     */
    public function testWrite()
    {
        $viewStrategy = new ViewStrategy('test');
        $result = $viewStrategy->write('test');

        $this->assertInstanceOf(ViewStrategyInterface::class, $result);
    }

    /**
     * @return void
     */
    public function testEngine()
    {
        $engine = $this->createViewStrategyMock();
        $viewStrategy = new ViewStrategy($engine);
        $result = $viewStrategy->engine();

        $this->assertEquals($engine, $result);
    }

    /**
     * @return void
     */
    public function testTo()
    {
        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn(4);

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withStatus')
            ->willReturnSelf();

        $engine = $this->createViewStrategyMock();

        $viewStrategy = new ViewStrategy($engine);
        $viewStrategy->write('test');

        $this->assertInstanceOf(
            ResponseInterface::class,
            $viewStrategy->to($response)
        );

        $engine->method('render')
            ->willReturn('test');

        $viewStrategy->render('test');

        $this->assertInstanceOf(
            ResponseInterface::class,
            $viewStrategy->to($response)
        );
    }

    /**
     * @return void
     */
    public function testToRequiresRenderableEngine()
    {
        $viewStrategy = new ViewStrategy(new stdClass());
        $viewStrategy->render('test');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Template engine must provide a render method.');

        $viewStrategy->to($this->createResponseMock());
    }

    /**
     * @return void
     */
    public function testToRequiresStringRenderResult()
    {
        $engine = new class () {
            /**
             * @param array<string, mixed> $params
             *
             * @return array<string, mixed>
             */
            public function render(string $view, array $params): array
            {
                return $params;
            }
        };

        $viewStrategy = new ViewStrategy($engine);
        $viewStrategy->render('test');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Template render must return a string.');

        $viewStrategy->to($this->createResponseMock());
    }

    private function createViewStrategyMock(): ViewStrategyInterface&Stub
    {
        return $this->createStub(ViewStrategyInterface::class);
    }

    private function createResponseMock(): ResponseInterface&Stub
    {
        return $this->createStub(ResponseInterface::class);
    }

    private function createStreamMock(): StreamInterface&Stub
    {
        return $this->createStub(StreamInterface::class);
    }
}
