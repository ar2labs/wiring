<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface FlashInterface
{
    /**
     * Add flash message for current request.
     *
     * @param string $key     The key to store the message under
     * @param string $message Message to show on next request
     */
    public function addMessageNow(string $key, string $message);

    /**
     * Get flash messages.
     *
     * @return array Messages to show for current request
     */
    public function getMessages(): array;

    /**
     * Get Flash Message.
     *
     * @param string $key  The key to get the message from
     *
     * @return string|null Returns the message
     */
    public function getMessage(string $key): ?string;
}
