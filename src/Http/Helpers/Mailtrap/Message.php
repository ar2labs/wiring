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
        $this->mailer->Subject = utf8_decode($subject);

        return $this;
    }

    /**
     * @param string $body
     *
     * @return self
     */
    public function body(?string $body)
    {
        $this->mailer->Body = !empty($body) ? utf8_decode($body) : '';

        return $this;
    }
}
