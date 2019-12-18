<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use Wiring\Http\Controller\AbstractJsonViewController;

class SimpleJsonViewController extends AbstractJsonViewController
{
    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        return $this->response;
    }
}
