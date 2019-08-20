<?php

declare(strict_types=1);

namespace Wiring\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * Emits a response via the header() function, and the body content
     * via the output buffer.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function emit(ResponseInterface $response): ResponseInterface
    {
        // Retrieves all message header values
        foreach ($response->getHeaders() as $name => $values) {
            // Set cookie status
            $cookie = stripos($name, 'Set-Cookie') === 0 ? false : true;
            // Get header value
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $cookie);
                $cookie = false;
            }
        }

        // Send a raw HTTP header
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ), true, $response->getStatusCode());

        // Checks no content is false
        if (in_array($response->getStatusCode(), [204, 205, 304]) === false) {
            // Gets the body as a stream
            $stream = $response->getBody();

            // Returns whether or not the stream is seekable
            if ($stream->isSeekable()) {
                // Seek to the beginning of the stream
                $stream->rewind();
            }

            // Get stream lenght
            $streamLenght = (!$response->getHeaderLine('Content-Length')) ?
                $stream->getSize() : $response->getHeaderLine('Content-Length');

            // While the stream is not the end of the stream.
            while (!$stream->eof()) {
                // Output one or more strings
                echo $stream->read(is_int($streamLenght) ?
                    $streamLenght : (int) $streamLenght);
            }
        }

        return $response;
    }
}
