<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use UnexpectedValueException;
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
        $json = $this->get(JsonStrategyInterface::class);

        if (!$json instanceof JsonStrategyInterface) {
            throw new UnexpectedValueException('JSON strategy interface not implemented.');
        }

        return $json;
    }

    /**
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
