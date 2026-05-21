<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface MailerInterface
{
    public string $Subject { get; set; }

    public string $Body { get; set; }

    /**
     * Add email address.
     */
    public function addAddress(string $email): void;

    /**
     * Send mail.
     */
    public function send(): bool;
}
