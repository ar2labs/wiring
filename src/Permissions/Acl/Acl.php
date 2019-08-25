<?php

declare(strict_types=1);

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
     * @var UserAclInterface
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
                throw new \InvalidArgumentException('Role is invalid!');
            }
        }

        $this->roles = $roles;

        foreach ($resources as $resource) {
            // Check resource instance
            if (!$resource instanceof Resource) {
                throw new \InvalidArgumentException('Resource is invalid!');
            }
        }

        $this->resources = $resources;
    }

    /**
     * Define a user.
     *
     * @param UserAclInterface $user
     *
     * @return self
     */
    public function setUser(UserAclInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Return has role.
     *
     * @param string $name
     *
     * @return Role|bool
     */
    public function hasRole(string $name)
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
     * Return has permission.
     *
     * @param string $role
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermission(string $role, string $permission): bool
    {
        // Check role
        $itemRole = $this->hasRole($role);

        if ($itemRole instanceof Role) {
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
     * Can permission.
     *
     * @param mixed                 $permission
     * @param UserAclInterface|null $user
     *
     * @return bool
     */
    public function can($permission, UserAclInterface $user = null): bool
    {
        if ($user) {
            $role = (string)$user->getRole()->getName();
            // Check role permission
            return $this->hasPermission($role, $permission);
        }

        if (method_exists($this->user, 'getRole')) {
            // Get role name
            $role = (string)$this->user->getRole()->getName();
            // Check role permission
            return $this->hasPermission($role, $permission);
        }

        // Whoops!
        return false;
    }

    /**
     * Cannot permission.
     *
     * @param mixed                 $permission
     * @param UserAclInterface|null $user
     *
     * @return bool
     */
    public function cannot($permission, UserAclInterface $user = null): bool
    {
        return !$this->can($permission, $user);
    }
}
