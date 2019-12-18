<?php

namespace Wiring\Interfaces;

interface MessageInterface
{
    /**
     * @param string $address
     *
     * @return self
     */
    public function to(string $address);

    /**
     * @param string $subject
     *
     * @return self
     */
    public function subject(string $subject);

    /**
     * @param string $body
     *
     * @return self
     */
    public function body(?string $body);
}
