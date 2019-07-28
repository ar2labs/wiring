<?php

declare(strict_types=1);

namespace Wiring;

use Wiring\Interfaces\ApplicationInterface;
use Wiring\Http\RequestHandler;

/**
 * Start a wiring application.
 */
class Application extends RequestHandler implements ApplicationInterface
{
    /**
     * Start then application.
     */
    public function run()
    {
        return $this->handle($this->request);
    }

    /**
     * Stop the application.
     */
    public function stop()
    {
        $this->setIsAfterMiddleware(true);

        return $this->handle($this->request);
    }
}
