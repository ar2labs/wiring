<?php

namespace Wiring\Http\Helpers;

use Wiring\Strategy\Mailtrap\Message;
use Wiring\Interfaces\ViewRendererInterface;

class Mailer
{
    /** @var mixed */
    protected $mailer;

    /** @var \Psr\Container\ContainerInterface $container */
    protected $container;

    /**
     * Mailer constructor.
     *
     * @param $mailer
     * @param $container
     */
    public function __construct($mailer, $container)
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

        $message->body($this->container->get(ViewRendererInterface::class)->render($template, [
            'data' => $data
        ]));

        call_user_func($callback, $message);

        $this->mailer->send();
    }
}
