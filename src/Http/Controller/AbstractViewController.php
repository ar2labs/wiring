<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use UnexpectedValueException;
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
        $view = $this->get(ViewStrategyInterface::class);

        if (!$view instanceof ViewStrategyInterface) {
            throw new UnexpectedValueException('View strategy interface not implemented.');
        }

        return $view;
    }
}
