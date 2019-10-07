<?php

namespace Wiring\Interfaces;

interface MessageInterface
{
    /**
     * @param string $address
     */
    public function to(string $address);

    /**
     * @param string $subject
     */
    public function subject(string $subject);

    /**
     * @param string $body
     */
    public function body(?string $body);
}
