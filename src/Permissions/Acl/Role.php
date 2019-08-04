<?php

declare(strict_types=1);

namespace Wiring\Permissions\Acl;

class Role
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * Role constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    /**
     * Get a role.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set a role.
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

    /**
     * Get a permission.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set a permission.
     *
     * @param Permission $permission
     *
     * @return self
     */
    public function addPermission(Permission $permission): self
    {
        $this->permissions[] = $permission;

        return $this;
    }
}
