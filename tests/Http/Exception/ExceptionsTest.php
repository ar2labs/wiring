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
    /**
     * @return void
     */
    public function testExceptions()
    {
        $exception = new BadRequestException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertSame(400, $exception->getStatusCode());

        $exception = new UnauthorizedException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertSame(401, $exception->getStatusCode());

        $exception = new MethodNotAllowedException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertSame(405, $exception->getStatusCode());

        $exception = new NotFoundException();

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertSame(404, $exception->getStatusCode());

        // Empty message test
        $exception = new HttpException(0, '', new HttpException(0, ''));

        $this->assertInstanceOf(HttpException::class, $exception);
        $this->assertSame(0, $exception->getStatusCode());

        $stream = $this->createStreamMock();
        $stream->method('isWritable')
            ->willReturn(true);

        $response = $this->createResponseMock();
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
