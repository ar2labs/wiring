<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface RouterInterface
{
    const NOT_FOUND = 0;
    const FOUND = 1;
    const METHOD_NOT_ALLOWED = 2;

    /**
     * Dispatches against the provided HTTP method and URI.
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array
     */
    public function dispatch(string $httpMethod, string $uri): array;
}
