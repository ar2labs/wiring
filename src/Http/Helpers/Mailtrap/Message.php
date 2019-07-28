<?php

namespace Wiring\Http\Helpers\Mailtrap;

class Message
{
    /** @var mixed */
    protected $mailer;

    /**
     * Message constructor.
     *
     * @param $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $address
     */
    public function to($address)
    {
        $this->mailer->addAddress($address);
    }

    /**
     * @param $subject
     */
    public function subject($subject)
    {
        $this->mailer->Subject = utf8_decode($subject);
    }

    /**
     * @param $body
     */
    public function body($body)
    {
        $this->mailer->Body = utf8_decode($body);
    }
}
