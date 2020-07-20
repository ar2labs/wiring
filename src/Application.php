<?php

declare(strict_types=1);

namespace Wiring;

use Psr\Http\Message\ResponseInterface;
use Wiring\Http\RequestHandler;
use Wiring\Interfaces\ApplicationInterface;

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
