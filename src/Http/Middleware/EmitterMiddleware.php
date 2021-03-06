<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Interfaces\EmitterInterface;

class EmitterMiddleware implements EmitterInterface, MiddlewareInterface
{
    /**
     * @var EmitterInterface|null
     */
    private $emitter;

    /**
     * Set your preferred emitter, this is optional.
     *
     * Note: Not set a interface for emitter,
     * no PSR have been set yet.
     *
     * @param EmitterInterface|null $emitter
     */
    public function __construct(?EmitterInterface $emitter = null)
    {
        $this->emitter = $emitter;
    }

    /**
     * Process an emitter for PSR-7 response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Handles a server request and produces a response
        $response = $handler->handle($request);

        // Checks if or where headers not have been sent
        if (headers_sent() === false) {
            // Check if emitter is set and emit method exist
            if ($this->emitter != null && method_exists($this->emitter, 'emit')) {
                // Emits for a emitter environment
                $this->emitter->emit($response);
            } else {
                // Emits the header and body content via the output buffer
                $this->emit($response);
            }
        }

        return $response;
    }

    /**
     * Emits a response with the body content via the output buffer.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function emit(ResponseInterface $response): ResponseInterface
    {
        // Emits a response via the header()
        $statusCode = $this->emitHeader($response);

        // Checks no content is false
        if (in_array($statusCode, [204, 205, 304]) === false) {
            // Gets the body as a stream
            $stream = $response->getBody();

            // Returns whether or not the stream is seekable
            if ($stream->isSeekable()) {
                // Seek to the beginning of the stream
                $stream->rewind();
            }

            // Get stream lenght
            $streamLenght = (!$response->getHeaderLine('Content-Length')) ?
                (int) $stream->getSize() :
                (int) $response->getHeaderLine('Content-Length');

            // While the stream is not the end of the stream and lenght > 0
            while (!$stream->eof()) {
                // Output one or more strings
                echo $stream->read($streamLenght);
                // Check stream lenght
                if ($streamLenght <= 0) {
                    break;
                }
            }
        }

        return $response;
    }

    /**
     * Emits a response via the header() function.
     *
     * @param ResponseInterface $response
     *
     * @return int Status Code
     */
    private function emitHeader(ResponseInterface $response)
    {
        // Retrieves all message header values
        foreach ($response->getHeaders() as $name => $values) {
            // Set cookie status
            $cookie = stripos($name, 'Set-Cookie') !== 0;
            // Get header value
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $cookie);
                $cookie = false;
            }
        }

        // Get Protocol Version
        $protocolVersion = $response->getProtocolVersion() != '' ?
            $response->getProtocolVersion() :
            $_SERVER['SERVER_PROTOCOL'];

        // Get Status Code
        $statusCode = $response->getStatusCode() != 200 ?
            $response->getStatusCode() :
            (int) http_response_code();

        // Get Reason Phrase
        $reasonPhrase = $response->getReasonPhrase() != '' ?
            $response->getReasonPhrase() :
         'Internal Server Error';

        // Send a raw HTTP header
        header(sprintf(
            'HTTP/%s %s %s',
            $protocolVersion,
            $statusCode,
            $reasonPhrase
        ), true, $statusCode);

        return $statusCode;
    }
}
