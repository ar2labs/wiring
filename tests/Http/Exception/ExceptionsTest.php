<?php

namespace Wiring\Tests\Http\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Wiring\Http\Exception\BadRequestException;
use Wiring\Http\Exception\HttpException;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Http\Exception\UnauthorizedException;

class ExceptionsTest extends TestCase
{
    public function testExceptions()
    {
        $exception = new BadRequestException;

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertEquals(400, $exception->getStatusCode());

        $exception = new MethodNotAllowedException;

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertEquals(405, $exception->getStatusCode());

        $exception = new NotFoundException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertEquals(404, $exception->getStatusCode());

        $exception = new UnauthorizedException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertEquals(401, $exception->getStatusCode());

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isWritable')
            ->willReturn(false);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('withStatus')
            ->willReturnSelf();
        $response->method('withAddedHeader')
            ->willReturnSelf();
        $response->method('getBody')
            ->willReturn($stream);

        $this->assertIsArray($exception->getHeaders());
        $this->assertIsArray($exception->getData());
        $this->assertInstanceOf(ResponseInterface::class, $exception->buildJsonResponse($response));
        $this->assertInstanceOf(ResponseInterface::class, $exception->buildResponse($response));
    }
}
