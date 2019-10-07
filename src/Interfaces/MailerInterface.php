<?php

namespace Wiring\Interfaces;

interface MailerInterface
{
    /**
     * Add email address.
     */
    public function addAddress(string $email): void;

    /**
     * Send mail.
     */
    public function send(): bool;
}
