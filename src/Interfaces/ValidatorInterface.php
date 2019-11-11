<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

interface ValidatorInterface
{
    /**
     * Set validation using input and rules.
     *
     * @param  array  $input
     * @param  array  $rules
     *
     * @return self
     */
    public function validate(array $input, $rules = []);

    /**
     * Checks if validation has passed.
     *
     * @return bool
     */
    public function passes();

    /**
     * Checks if validation has failed.
     *
     * @return bool
     */
    public function fails();

    /**
     * Get errors and return.
     *
     * @return array
     */
    public function errors();

    /**
     * Adds a custom rule message.
     *
     * @param string $rule
     * @param string $message
     */
    public function addRuleMessage($rule, $message);

    /**
     * Adds custom rule messages.
     *
     * @param array $messages
     */
    public function addRuleMessages(array $messages);

    /**
     * Adds a custom field message.
     *
     * @param string $field
     * @param string $rule
     * @param string $message
     */
    public function addFieldMessage($field, $rule, $message);

    /**
     * Adds custom field messages
     *
     * @param array $messages
     */
    public function addFieldMessages(array $messages);

    /**
     * Add a custom rule
     *
     * @param string $name
     * @param \Closure $callback
     */
    public function addRule($name, \Closure $callback);
}
