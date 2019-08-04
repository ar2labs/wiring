<?php

declare(strict_types=1);

namespace Wiring\Strategy;

use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\StrategyInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var array */
    protected $defaultResponseHeaders = [];

    /**
     * Get current default response headers.
     *
     * @return array
     */
    public function getDefaultResponseHeaders(): array
    {
        return $this->defaultResponseHeaders;
    }

    /**
     * Add or replace a default response header.
     *
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function addDefaultResponseHeader(string $name, string $value): self
    {
        $this->defaultResponseHeaders[strtolower($name)] = $value;

        return $this;
    }

    /**
     * Add multiple default response headers.
     *
     * @param array $headers
     *
     * @return self
     */
    public function addDefaultResponseHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->addDefaultResponseHeader($name, $value);
        }

        return $this;
    }

    /**
     * Apply default response headers.
     *
     * Headers that already exist on the response will NOT be replaced.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function applyDefaultResponseHeaders(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->defaultResponseHeaders as $name => $value) {
            if (false === $response->hasHeader($name)) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }
}
