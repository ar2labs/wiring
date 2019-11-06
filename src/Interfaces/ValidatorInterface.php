<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ValidatorInterface
{
    /**
     * Get validator instance.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function validator();
}
