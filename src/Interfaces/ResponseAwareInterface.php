<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ResponseAwareInterface
{
    /**
     * Get the current response
     *
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;

    /**
     * Set the response implementation
     *
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function setResponse(
        ResponseInterface $response
    );
}
