<?php

declare(strict_types=1);

namespace Wiring;

use Wiring\Interfaces\ApplicationInterface;
use Wiring\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;

class Application extends RequestHandler implements ApplicationInterface
{
    /**
     * Start the application.
     *
     * @return ResponseInterface
     */
    public function run(): ResponseInterface
    {
        return $this->handle($this->request);
    }

    /**
     * Stop the application.
     *
     * @return ResponseInterface
     */
    public function stop(): ResponseInterface
    {
        $this->setIsAfterMiddleware(true);

        return $this->handle($this->request);
    }
}
