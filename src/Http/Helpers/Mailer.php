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
     * @param $mailer
     * @param $container
     */
    public function __construct($mailer, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->container = $container;
    }

    /**
     * Send mail.
     *
     * @param $template
     * @param $data
     * @param $callback
     */
    public function send($template, $data, $callback)
    {
        $message = new Message($this->mailer);

        $message->body($this->container->get(ViewStrategyInterface::class)
            ->render($template, [
                'data' => $data
            ]));

        call_user_func($callback, $message);

        $this->mailer->send();
    }
}
