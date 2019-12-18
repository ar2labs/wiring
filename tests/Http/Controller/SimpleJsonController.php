<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use Wiring\Http\Controller\AbstractJsonController;

class SimpleJsonController extends AbstractJsonController
{
    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        return $this->response;
    }
}
