<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Wiring\Interfaces\HttpExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class HttpException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \Exception|null
     */
    protected $previous;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param int        $status
     * @param string     $message
     * @param \Exception $previous
     * @param array      $headers
     * @param int        $code
     */
    public function __construct(
        int        $status,
        string     $message = '',
        \Exception $previous = null,
        array      $headers = [],
        int        $code = 0
    ) {
        $this->status = $status;
        $this->message = $message;
        $this->previous = $previous;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Return the status code of the http exceptions.
     *
     * @return integer
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Return an array of headers provided when the exception was thrown.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Return an array of data provided when the exception was thrown.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Return an error into an HTTP or JSON data array.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function buildResponse(
        ResponseInterface $response
    ): ResponseInterface {
        foreach ($this->headers as $key => $value) {
            $response = $response->withAddedHeader($key, $value);
        }

        if ((empty($this->message)) && ($this->previous instanceof \Exception)) {
            $this->message = $this->previous->getMessage();
        }

        $statusCode = ($this->previous instanceof \Exception) ?
            $this->previous->getCode() : null;

        // Check status code is null
        if ($statusCode == null) {
            $statusCode = $this->code >= 100 && $this->code <= 500 ?
                $this->code : 400;
        }

        $this->data = [
            'type' => $this->previous ? get_class($this->previous) : 'error',
            'code' => $statusCode,
            'message' => $this->message,
        ];

        $response->withStatus($statusCode);

        return $response->withStatus($this->status, $this->message);
    }

    /**
     * Accepts a response object and builds it in
     * to a json representation of the exception.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function buildJsonResponse(
        ResponseInterface $response
    ): ResponseInterface {
        $this->headers['content-type'] = 'application/json';

        foreach ($this->headers as $key => $value) {
            $response = $response->withAddedHeader($key, $value);
        }

        if ((empty($this->message)) && ($this->previous instanceof \Exception)) {
            $this->message = $this->previous->getMessage();
        }

        if ($response->getBody()->isWritable()) {
            $json = json_encode([
                'code' => $this->status,
                'status' => 'error',
                'message' => $this->message,
                'data' => [],
            ]);

            if (is_string($json)) {
                $response->getBody()->write($json);
            }
        }

        return $response->withStatus($this->status, $this->message);
    }
}
