<?php

declare(strict_types=1);

namespace Wiring;

use Wiring\Interfaces\ApplicationInterface;
use Wiring\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;

class Application extends RequestHandler implements ApplicationInterface
{
    /**
     * @return ResponseInterface
     * @throws Http\Exception\ErrorHandler
     */
    public function run(): ResponseInterface
    {
        return $this->handle($this->request);
    }

    /**
     * @return ResponseInterface
     * @throws Http\Exception\ErrorHandler
     */
    public function stop(): ResponseInterface
    {
        $this->setIsAfterMiddleware();

        return $this->handle($this->request);
    }
}
