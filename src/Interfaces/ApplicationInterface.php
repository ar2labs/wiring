<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ApplicationInterface
{
    /**
     * Start then application.
     *
     * @return ResponseInterface
     */
    public function run(): ResponseInterface;

    /**
     * Stop the application.
     *
     * @return ResponseInterface
     */
    public function stop(): ResponseInterface;
}
