<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Exception;

use Exception;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Wiring\Http\Exception\ErrorHandler;

class ErrorHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testErrorHandler()
    {
        // Debug mode test
        $request = $this->createRequestMock();
        $request->method('getHeader')
            ->willReturn(['1']);

        $response = $this->createResponseMock();
        $response->method('getStatusCode')
            ->willReturn(200);

        $exception = new Exception('Not Found', 404);
        $logger = $this->createLoggerMock();

        $errorHandler = new ErrorHandler($request, $response, $exception, $logger);

        // JSON test
        $requestJSON = $this->createRequestMock();
        $requestJSON->method('getHeader')
            ->willReturn(['application/json']);

        $errorHandlerJSON = new ErrorHandler($requestJSON, $response, $exception, $logger, [], true);

        $this->assertArrayHasKey(ErrorHandler::ERROR_MESSAGE, $errorHandler->error());
        $this->assertInstanceOf(Exception::class, $errorHandler->getException());
        $this->assertArrayHasKey(ErrorHandler::ERROR_MESSAGE, $errorHandlerJSON->error());
        $this->assertTrue($errorHandlerJSON->isJson());
    }

    /**
     * @return void
     */
    public function testProductionErrorDoesNotExposeExceptionDetails()
    {
        $request = $this->createRequestMock();
        $request->method('getHeader')
            ->willReturnMap([
                [ErrorHandler::CONTENT_TYPE, []],
                [ErrorHandler::DEBUG_MODE, ['0']],
            ]);

        $response = $this->createResponseMock();
        $response->method('getStatusCode')
            ->willReturn(500);

        $exception = new Exception('password=secret <script>alert(1)</script>', 500);
        $errorHandler = new ErrorHandler($request, $response, $exception);

        $error = $errorHandler->error('<script>alert("x")</script>');
        $encodedError = json_encode($error);

        $this->assertIsString($encodedError);
        $this->assertSame(
            '<span>&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;</span>',
            $error[ErrorHandler::ERROR_MESSAGE]
        );
        $this->assertArrayNotHasKey(ErrorHandler::ERROR_FILE, $error);
        $this->assertArrayNotHasKey(ErrorHandler::ERROR_TRACE, $error);
        $this->assertStringNotContainsString('password=secret', $encodedError);
    }

    /**
     * @return void
     */
    public function testJsonErrorWithCharsetUsesDefaultProductionMessageAndRedactsLogs()
    {
        $request = $this->createRequestMock();
        $request->method('getHeader')
            ->willReturnMap([
                [ErrorHandler::CONTENT_TYPE, ['application/json; charset=utf-8']],
                [ErrorHandler::DEBUG_MODE, ['0']],
            ]);

        $response = $this->createResponseMock();
        $response->method('getStatusCode')
            ->willReturn(500);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->callback(static fn (string $message): bool =>
                    str_contains($message, 'password=[redacted]') &&
                    !str_contains($message, 'super-secret')),
                $this->callback(static function (array $context): bool {
                    $nested = $context['nested'] ?? null;

                    return ($context['token'] ?? null) === '[redacted]' &&
                        is_array($nested) &&
                        ($nested['api_key'] ?? null) === '[redacted]' &&
                        ($context['safe'] ?? null) === 'keep';
                })
            );

        $errorHandler = new ErrorHandler(
            $request,
            $response,
            new Exception('Database failed password=super-secret', 500),
            $logger,
            [
                'token' => 'abc123',
                'nested' => ['api_key' => 'secret-key'],
                'safe' => 'keep',
            ]
        );

        $error = $errorHandler->error();

        $this->assertTrue($errorHandler->isJson());
        $this->assertSame(ErrorHandler::DEFAULT_MESSAGE, $error[ErrorHandler::ERROR_MESSAGE]);
    }

    private function createRequestMock(): ServerRequestInterface&Stub
    {
        return $this->createStub(ServerRequestInterface::class);
    }

    private function createResponseMock(): ResponseInterface&Stub
    {
        return $this->createStub(ResponseInterface::class);
    }

    private function createLoggerMock(): LoggerInterface&Stub
    {
        return $this->createStub(LoggerInterface::class);
    }
}
