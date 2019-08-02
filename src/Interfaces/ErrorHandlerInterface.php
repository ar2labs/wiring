<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ErrorHandlerInterface
{
    /**
     * Return an error into an HTTP or JSON data array.
     *
     * @param string $message
     *
     * @return array
     */
    public function error(?string $message = null): array;

    /**
     * Get exception.
     *
     * @return \Exception|\Throwable
     */
    public function getException();

    /**
     * Check is JSON.
     *
     * @return bool
     */
    public function isJson(): bool;
}
