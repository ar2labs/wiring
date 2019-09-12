<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Interfaces\ViewStrategyInterface;

abstract class AbstractJsonViewController extends AbstractController
{
    /**
     * @return JsonStrategyInterface
     * @throws \Exception
     */
    public function json(): JsonStrategyInterface
    {
        return $this->get(JsonStrategyInterface::class);
    }

    /**
     * @return ViewStrategyInterface
     * @throws \Exception
     */
    public function view(): ViewStrategyInterface
    {
        return $this->get(ViewStrategyInterface::class);
    }
}
