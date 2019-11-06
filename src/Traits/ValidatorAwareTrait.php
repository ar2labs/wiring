<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\ValidatorInterface;

trait ValidatorAwareTrait
{
    /**
     * @var ValidatorInterface|null
     */
    protected $validator;

    /**
     * Get the current validator.
     *
     * @return ValidatorInterface|null
     */
    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * Set the validator implementation.
     *
     * @param ValidatorInterface $validator
     *
     * @return void
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get validator instance.
     *
     * @throws \Exception
     *
     * @return ValidatorInterface
     */
    public function validator(): ValidatorInterface
    {
        if (!$this->has(ValidatorInterface::class)) {
            throw new \BadFunctionCallException('Validator interface not implemented.');
        }

        return $this->get(ValidatorInterface::class);
    }
}
