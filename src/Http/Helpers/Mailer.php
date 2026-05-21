<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

use Psr\Container\ContainerInterface;
use UnexpectedValueException;
use Wiring\Http\Helpers\Mailtrap\Message;
use Wiring\Interfaces\MailerInterface;
use Wiring\Interfaces\ViewStrategyInterface;

class Mailer
{
    /** @var MailerInterface */
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
        if (!$mailer instanceof MailerInterface) {
            throw new UnexpectedValueException('Mailer interface not implemented.');
        }

        $this->mailer = $mailer;
        $this->container = $container;
    }

    /**
     * Create a message and send it.
     *
     * @param string   $template
    * @param array<string, mixed> $data
     * @param callable $callback
     *
     * @return bool
     */
    public function send(string $template, array $data, callable $callback): bool
    {
        $view = $this->container->get(ViewStrategyInterface::class);

        if (!$view instanceof ViewStrategyInterface) {
            throw new UnexpectedValueException('View strategy interface not implemented.');
        }

        $renderer = [$view->engine(), 'render'];

        if (!is_callable($renderer)) {
            throw new UnexpectedValueException('Template engine must provide a render method.');
        }

        $html = $renderer($template, $data);

        if (!is_string($html)) {
            throw new UnexpectedValueException('Template render must return a string.');
        }

        $message = new Message($this->mailer);
        $message->body($html);

        call_user_func($callback, $message);

        return $this->mailer->send();
    }
}
