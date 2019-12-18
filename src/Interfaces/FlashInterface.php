<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface FlashInterface
{
    /**
     * Add flash message for the next request.
     *
     * @param string $key The key to store the message under
     * @param mixed  $message Message to show on next request
     *
     * @return self
     */
    public function addMessage(string $key, $message);

    /**
     * Add flash message for current request.
     *
     * @param string $key The key to store the message under
     * @param string $message Message to show for the current request
     *
     * @return self
     */
    public function addMessageNow(string $key, string $message);

    /**
     * Has Flash Message.
     *
     * @param string $key The key to get the message from
     *
     * @return bool Whether the message is set or not
     */
    public function hasMessage(string $key): bool;

    /**
     * Get Flash Message.
     *
     * @param string $key The key to get the message from
     *
     * @return string|null Returns the message
     */
    public function getMessage(string $key): ?string;

    /**
     * Get flash messages.
     *
     * @return array Messages to show for current request
     */
    public function getMessages(): array;

    /**
     * Clear specific message.
     *
     * @param string $key The key to clear
     *
     * @return void
     */
    public function clearMessage(string $key);

    /**
     * Clear all messages.
     *
     * @return void
     */
    public function clearMessages();
}
