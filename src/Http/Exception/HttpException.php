<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\HttpExceptionInterface;

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

    /** @var array<string, string|array<string>> */
    protected $headers = [];

    /** @var array<string, mixed> */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param int        $status
     * @param string     $message
    * @param \Exception|null $previous
    * @param array<string, string|array<string>> $headers
     * @param int        $code
     */
    public function __construct(
        int        $status,
        string     $message = '',
        ?\Exception $previous = null,
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
    * @return array<string, string|array<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Return an array of data provided when the exception was thrown.
     *
    * @return array<string, mixed>
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
            $this->previous->getCode() : $this->getCode();

        // Check status code is valid
        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = $this->getCode() >= 100 && $this->getCode() <= 599 ?
                $this->getCode() : 400;
        }

        $this->data = [
            'type' => $this->previous ? get_class($this->previous) : 'error',
            'code' => $statusCode,
            'message' => $this->message,
        ];

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
