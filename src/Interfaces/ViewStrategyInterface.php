<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ViewStrategyInterface
{
    /**
     * Define template engine.
     *
     * @param $engine
     */
    public function __construct($engine);

    /**
     * Get template engine.
     *
     * @return mixed
     */
    public function engine();

    /**
     * Render a new template view.
     *
     * @param string $view Template view name
     * @param array $params View params
     *
     * @return self
     */
    public function render($view, array $params = []);

    /**
     * Write data to the stream.
     *
     * @param string $data The string that is to be written.
     *
     * @return self
     */
    public function write($data);

    /**
     * Return response with JSON header and status.
     *
     * @param ResponseInterface $response
     * @param int $status
     *
     * @return ResponseInterface
     */
    public function to(ResponseInterface $response, $status = 200): ResponseInterface;
}
