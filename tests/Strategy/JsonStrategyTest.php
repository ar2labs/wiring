<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use UnexpectedValueException;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Strategy\JsonStrategy;

final class JsonStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testRender()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->render(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategy::class, $result);
    }

    /**
     * @return void
     */
    public function testWrite()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->write(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategyInterface::class, $result);
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function testTo()
    {
        $stream = $this->createStreamMock();
        $stream->method('write')
            ->willReturn(16);

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withStatus')
            ->willReturnSelf();

        $response->method('withHeader')
            ->willReturnSelf();

        $jsonStrategy = new JsonStrategy();
        $jsonStrategy->write('test');

        $this->assertInstanceOf(
            ResponseInterface::class,
            $jsonStrategy->to($response)
        );

        $jsonStrategy->write(['key1' => 'value1']);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $jsonStrategy->to($response)
        );

        $array = ['key1' => 'value1', 'key2' => 'value2'];

        $jsonStrategy->render($array);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $jsonStrategy->to($response)
        );

        try {
            $resource = fopen('phpunit.xml.dist', 'r');
            $jsonStrategy->render($resource);
            $this->assertInstanceOf(
                ResponseInterface::class,
                $jsonStrategy->to($response)
            );
        } catch (InvalidArgumentException $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Cannot JSON encode resources', $e->getMessage());
        }

        try {
            $text = "\xB1\x31";
            $jsonStrategy->render($text);
            $this->assertInstanceOf(
                ResponseInterface::class,
                $jsonStrategy->to($response)
            );
        } catch (InvalidArgumentException $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Unable to encode data to JSON in ' .
                'Wiring\Strategy\JsonStrategy: Malformed UTF-8 characters, ' .
                'possibly incorrectly encoded', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testToClearsStateAfterWriting()
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with('test')
            ->willReturn(4);

        $response = $this->createResponseMock();
        $response->method('getBody')
            ->willReturn($stream);
        $response->method('withStatus')
            ->willReturnSelf();
        $response->method('withHeader')
            ->willReturnSelf();

        $jsonStrategy = new JsonStrategy();
        $jsonStrategy->write('test')->to($response);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('JSON strategy has no data to write.');

        $jsonStrategy->to($response);
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
