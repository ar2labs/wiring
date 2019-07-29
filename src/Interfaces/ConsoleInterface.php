<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ConsoleInterface
{
    /**
     * Write output to console browser.
     *
     * @var object|string $obj
     */
    public function log($obj);
}