<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Wiring\Interfaces\ViewStrategyInterface;

abstract class AbstractViewController extends AbstractController
{
    /**
     *  Get a view renderer.
     *
     * @return ViewStrategyInterface
     * @throws \Exception
     */
    public function view(): ViewStrategyInterface
    {
        return $this->get(ViewStrategyInterface::class);
    }
}
