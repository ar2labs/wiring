<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Psr\Http\Message\ResponseInterface;

trait ResponseAwareTrait
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Get the current response.
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set the response implementation.
     *
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
