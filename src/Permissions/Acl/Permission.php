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
    public function __construct(?string $name = '')
    {
        $this->name = $name;
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
     * @param  string $name
     *
     * @return Permission
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
