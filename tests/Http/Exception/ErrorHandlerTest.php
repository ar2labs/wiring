<?php

namespace Wiring\Tests\Http\Exception;

use Exception;
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
        $exception = new Exception('Not Found', 404);
        $logger = $this->createLoggerMock();

        $errorHandler = new ErrorHandler($request, $response, $exception, $logger);

        // JSON test
        $requestJSON = $this->createRequestMock();
        $requestJSON->method('getHeader')
            ->willReturn(['application/json']);

        $errorHandlerJSON = new ErrorHandler($requestJSON, $response, $exception, $logger, [], true);

        $this->assertIsArray($errorHandler->error());
        $this->assertInstanceOf(Exception::class, $errorHandler->getException());
        $this->assertIsArray($errorHandlerJSON->error());
        $this->assertIsBool($errorHandlerJSON->isJson());
    }

    /**
     * @return mixed
     */
    private function createRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
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
    private function createLoggerMock()
    {
        return $this->createMock(LoggerInterface::class);
    }
}
