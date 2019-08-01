<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\ResponseAwareInterface;

trait ResponseAwareTrait
{
    /**
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * Get the current response.
     *
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set the response implementation.
     *
     * @param ResponseInterface $response
     *
     * @return ViewStrategyInterface
     */
    public function setResponse(ResponseInterface $response): ResponseAwareInterface
    {
        $this->response = $response;

        return $this;
    }
}
