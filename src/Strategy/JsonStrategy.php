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
     * @param mixed $data            The data array or object
     * @param int   $encodingOptions JSON encoding options
     *
     * @return self
     */
    public function render(
        $data,
        int $encodingOptions = 0
    ): self {
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
     * @param ResponseInterface|null $response
     * @param int                    $status
     *
     * @return ResponseInterface
     */
    public function to(
        ?ResponseInterface $response,
        int $status = 200
    ): ResponseInterface {
        // Check if it is to use json encode
        if ($this->isRender) {
            $response
                ->getBody()
                ->write($this->encode($this->data, $this->encodingOptions));
        } else {
            $response
                ->getBody()
                ->write($this->data);
        }

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * Encode the provided data to JSON.
     *
     * @param array|mixed $data            The data
     * @param int         $encodingOptions JSON encoding options
     *
     * @return string
     *
     * @throws InvalidArgumentException if unable to encode the $data to JSON
     */
    private function encode($data, int $encodingOptions): string
    {
        if (is_resource($data)) {
            throw new InvalidArgumentException('Cannot JSON encode resources');
        }

        $json = json_encode($data, $encodingOptions);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'Unable to encode data to JSON in %s: %s',
                __CLASS__,
                json_last_error_msg()
            ));
        }

        if (!\is_string($json)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to encode data to JSON in %s',
                __CLASS__
            ));
        }

        return $json;
    }
}
