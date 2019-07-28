<?php

namespace Wiring\Strategy;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\JsonStrategyInterface;

class JsonStrategy implements JsonStrategyInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $encodingOptions = 0;

    /**
     * @var bool
     */
    protected $isRender = false;

    /**
     * Write data with JSON encode.
     *
     * @param array $data The data
     * @param int $encodingOptions JSON encoding options
     *
     * @return self
     */
    public function render($data, $encodingOptions = 0): JsonStrategyInterface
    {
        $this->data = $data;
        $this->encodingOptions = $encodingOptions;
        $this->isRender = true;

        return $this;
    }

    /**
     * Write JSON to data response.
     *
     * @param mixed $data The data
     *
     * @return self
     */
    public function write($data): JsonStrategyInterface
    {
        $this->data = $data;
        $this->isRender = false;

        return $this;
    }

    /**
     * Return response with JSON header and status.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param int $status
     *
     * @return ResponseInterface
     */
    public function to(ResponseInterface $response, $status = 200): ResponseInterface
    {
        if ($this->isRender) {
            $response->getBody()->write($this->jsonEncode($this->data, $this->encodingOptions));
        } else {
            $response->getBody()->write($this->data);
        }

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * Encode the provided data to JSON.
     *
     * @param array $data The data
     * @param int $encodingOptions JSON encoding options
     *
     * @return string JSON
     *
     * @throws InvalidArgumentException if unable to encode the $data to JSON
     */
    private function jsonEncode($data, $encodingOptions)
    {
        if (is_resource($data)) {
            throw new InvalidArgumentException('Cannot JSON encode resources');
        }

        // Clear json_last_error()
        json_encode(null);

        $json = json_encode($data, $encodingOptions);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'Unable to encode data to JSON in %s: %s',
                __CLASS__,
                json_last_error_msg()
            ));
        }

        return $json;
    }
}
