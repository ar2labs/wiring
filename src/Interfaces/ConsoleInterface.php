<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ConsoleInterface
{
    /**
     * Write output to console browser.
     *
     * @param mixed $obj
     *
     * @return void|null
     */
    public function log($obj);
}
