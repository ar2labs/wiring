<?php

declare(strict_types=1);

namespace Wiring\Strategy;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;
use Wiring\Interfaces\JsonStrategyInterface;

class JsonStrategy implements JsonStrategyInterface
{
    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var int
     */
    protected int $encodingOptions = 0;

    /**
     * @var bool
     */
    protected bool $isRender = false;

    protected bool $hasData = false;

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
        $this->hasData = true;

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
        $this->encodingOptions = 0;
        $this->isRender = false;
        $this->hasData = true;

        return $this;
    }

    /**
     * Return response with JSON header and status.
     *
     * @param ResponseInterface $response
     * @param int               $status
     *
     * @return ResponseInterface
     */
    public function to(
        ResponseInterface $response,
        int $status = 200
    ): ResponseInterface {
        if (!$this->hasData) {
            throw new UnexpectedValueException('JSON strategy has no data to write.');
        }

        try {
            // Check if it is to use json encode
            if ($this->isRender) {
                $body = $this->encode($this->data, $this->encodingOptions);
            } else {
                $body = is_string($this->data) ? $this->data : $this->encode($this->data, 0);
            }

            $response
                ->getBody()
                ->write($body);

            return $response
                ->withStatus($status)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
        } finally {
            $this->reset();
        }
    }

    private function reset(): void
    {
        $this->data = null;
        $this->encodingOptions = 0;
        $this->isRender = false;
        $this->hasData = false;
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

        return (string) $json;
    }
}
