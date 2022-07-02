<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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
     * @return mixed
     */
    private function createResponseMock()
    {
        return $this->createMock(ResponseInterface::class);
    }

    /**
     * @return mixed
     */
    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }
}
