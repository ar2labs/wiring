<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ApplicationInterface
{
    /**
     * Starting application.
     */
    public function run();

    /**
     * Stopping application.
     */
    public function stop();
}
