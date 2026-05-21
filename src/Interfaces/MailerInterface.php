<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

/**
 * @property string $Subject
 * @property string $Body
 */
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
