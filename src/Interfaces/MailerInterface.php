<?php

namespace Wiring\Interfaces;

interface MailerInterface
{
    /**
     * Send mail.
     *
     * @param mixed    $template
     * @param mixed    $data
     * @param callable $callback
     */
    public function send($template, $data, $callback);
}
