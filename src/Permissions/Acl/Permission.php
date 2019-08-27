<?php

declare(strict_types=1);

namespace Wiring\Permissions\Acl;

class Permission
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Permission constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        $this->name = (string) $name;
    }

    /**
     * Get a permission.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set a permission.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
