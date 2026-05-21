<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers\Mailtrap;

use UnexpectedValueException;
use Wiring\Interfaces\MailerInterface;

class Message
{
    /** @var MailerInterface */
    protected $mailer;

    /**
     * Message constructor.
     *
     * @param mixed $mailer
     */
    public function __construct($mailer)
    {
        if (!$mailer instanceof MailerInterface) {
            throw new UnexpectedValueException('Mailer interface not implemented.');
        }

        $this->mailer = $mailer;
    }

    /**
     * @param string $address
     *
     * @return self
     */
    public function to(string $address)
    {
        $this->mailer->addAddress($address);

        return $this;
    }

    /**
     * @param string $subject
     *
     * @return self
     */
    public function subject(string $subject)
    {
        /** @phpstan-ignore-next-line PHPMailer-style mailers expose Subject as a public property. */
        $this->mailer->Subject = mb_convert_encoding($subject, 'ISO-8859-1', 'UTF-8');

        return $this;
    }

    /**
     * @param string $body
     *
     * @return self
     */
    public function body(?string $body)
    {
        /** @phpstan-ignore-next-line PHPMailer-style mailers expose Body as a public property. */
        $this->mailer->Body = !empty($body) ? mb_convert_encoding($body, 'ISO-8859-1', 'UTF-8') : '';

        return $this;
    }
}
