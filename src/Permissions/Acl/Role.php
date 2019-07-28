<?php

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
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Permission $permission
     *
     * @return self
     */
    public function addPermission(Permission $permission)
    {
        $this->permissions[] = $permission;

        return $this;
    }
}
