<?php

namespace Wiring\Http\Helpers;

use Wiring\Http\Helpers\Mailtrap\Message;
use Wiring\Interfaces\ViewStrategyInterface;
use Psr\Container\ContainerInterface;

class Mailer
{
    /** @var mixed */
    protected $mailer;

    /** @var ContainerInterface $container */
    protected $container;

    /**
     * Mailer constructor.
     *
     * @param mixed              $mailer
     * @param ContainerInterface $container
     */
    public function __construct($mailer, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->container = $container;
    }

    /**
     * Create a message and send it.
     *
     * @param string   $template
     * @param array    $data
     * @param callable $callback
     *
     * @return bool
     */
    public function send(string $template, array $data, callable $callback): bool
    {
        $view = $this->container->get(ViewStrategyInterface::class);
        $html = $view->engine()->render($template, $data);

        $message = new Message($this->mailer);
        $message->body($html);

        call_user_func($callback, $message);

        return $this->mailer->send();
    }
}
