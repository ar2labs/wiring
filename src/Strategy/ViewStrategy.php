<?php

declare(strict_types=1);

namespace Wiring\Strategy;

use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;
use Wiring\Interfaces\ViewStrategyInterface;

class ViewStrategy implements ViewStrategyInterface
{
    /**
     * @var mixed
     */
    protected $engine;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    protected $view;

    /** @var array<string, mixed> */
    protected $params;

    /**
     * @var bool
     */
    protected $isRender = false;

    /**
     * Define template engine.
     *
     * @param mixed $engine
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    /**
     * Get template engine.
     *
     * @return mixed
     */
    public function engine()
    {
        return $this->engine;
    }

    /**
     * Render a new template view.
     *
     * @param string $view Template view name
    * @param array<string, mixed> $params View params
     *
     * @return self
     */
    public function render($view, array $params = []): self
    {
        $this->view = $view;
        $this->params = $params;
        $this->isRender = true;

        return $this;
    }

    /**
     * Write data to the stream.
     *
     * @param string $data The string that is to be written.
     *
     * @return ViewStrategyInterface
     */
    public function write(string $data): ViewStrategyInterface
    {
        $this->data = $data;
        $this->isRender = false;

        return $this;
    }

    /**
     * Return response with JSON header and status.
     *
     * @param ResponseInterface $response
     * @param int               $status
     *
     * @return ResponseInterface
     */
    public function to(ResponseInterface $response, int $status = 200): ResponseInterface
    {
        if ($this->view) {
            $renderer = [$this->engine(), 'render'];

            if (!is_callable($renderer)) {
                throw new UnexpectedValueException('Template engine must provide a render method.');
            }

            $content = $renderer($this->view, $this->params);

            if (!is_string($content)) {
                throw new UnexpectedValueException('Template render must return a string.');
            }

            $response->getBody()->write($content);
        } elseif ($this->data) {
            $response->getBody()->write($this->data);
        }

        return $response->withStatus($status);
    }
}
