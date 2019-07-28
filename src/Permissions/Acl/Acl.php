<?php

namespace Wiring\Permissions\Acl;

use Wiring\Interfaces\UserAclInterface;

class Acl
{
    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @var \Wiring\Interfaces\UserAclInterface
     */
    protected $user;

    /**
     * Acl constructor.
     *
     * @param array $roles
     * @param array $resources
     */
    public function __construct(array $roles, array $resources)
    {
        foreach ($roles as $role) {
            // Check role instance
            if (!$role instanceof Role) {
                throw new \InvalidArgumentException("Role is invalid!");
            }
        }

        $this->roles = $roles;

        foreach ($resources as $resource) {
            // Check resource instance
            if (!$resource instanceof Resource) {
                throw new \InvalidArgumentException("Resource is invalid!");
            }
        }

        $this->resources = $resources;
    }

    /**
     * @param UserAclInterface $user
     *
     * @return self
     */
    public function setUser(UserAclInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param $name
     *
     * @return \Wiring\Permissions\Acl\Role|bool
     */
    public function hasRole($name)
    {
        /** @var \Wiring\Permissions\Acl\Role $role */
        foreach ($this->roles as $role) {
            // Check role name
            if ($role->getName() == $name) {
                // Yes, I've got the role!
                return $role;
            }
        }

        // Whoops!
        return false;
    }

    /**
     * @param string $role
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermission($role, $permission)
    {
        // Check role
        if ($itemRole = $this->hasRole($role)) {
            /** @var \Wiring\Permissions\Acl\Permission $itemPermission */
            foreach ($itemRole->getPermissions() as $itemPermission) {
                // Check permission
                if ($itemPermission->getName() == $permission) {
                    // Yes, I've got the power!
                    return true;
                }
            }
        }

        // Whoops!
        return false;
    }

    /**
     * @param $permission
     * @param \Wiring\Interfaces\UserAclInterface|null $user
     *
     * @return bool
     */
    public function can($permission, UserAclInterface $user = null)
    {
        if ($user) {
            // Check role permission
            return $this->hasPermission($user->getRole()->getName(), $permission);
        }

        if ($this->user) {
            // Check role permission
            return $this->hasPermission($this->user->getRole()->getName(), $permission);
        }

        // Whoops!
        return false;
    }

    /**
     * @param $permission
     * @param UserAclInterface|null $user
     *
     * @return bool
     */
    public function cannot($permission, UserAclInterface $user = null)
    {
        return !$this->can($permission, $user);
    }
}
