<?php

namespace Wiring\Http\Helpers\Mailtrap;

class Message
{
    /** @var mixed */
    protected $mailer;

    /**
     * Message constructor.
     *
     * @param mixed $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $address
     */
    public function to(string $address)
    {
        $this->mailer->addAddress($address);
    }

    /**
     * @param string $subject
     */
    public function subject(string $subject)
    {
        $this->mailer->Subject = utf8_decode($subject);
    }

    /**
     * @param string $body
     */
    public function body(?string $body)
    {
        $this->mailer->Body = !empty($body) ? utf8_decode($body) : '';
    }
}
