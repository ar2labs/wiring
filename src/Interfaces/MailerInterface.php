<?php

namespace Wiring\Interfaces;

interface MailerInterface
{
    /**
     * Send mail.
     *
     * @param $template
     * @param $data
     * @param $callback
     */
    public function send($template, $data, $callback);
}
