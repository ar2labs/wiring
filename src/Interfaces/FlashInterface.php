<?php

namespace Wiring\Interfaces;

interface FlashInterface
{
    /**
     * Add flash message for current request.
     *
     * @param string $key The key to store the message under
     * @param mixed $message Message to show on next request
     */
    public function addMessageNow($key, $message);

    /**
     * Get flash messages.
     *
     * @return array Messages to show for current request
     */
    public function getMessages();

    /**
     * Get Flash Message.
     *
     * @param string $key The key to get the message from
     * @return mixed|null Returns the message
     */
    public function getMessage($key);
}
