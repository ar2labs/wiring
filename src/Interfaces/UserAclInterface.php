<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Wiring\Permissions\Acl\Role;

interface UserAclInterface
{
    /**
     * Get a user role.
     *
     * @return Role
     */
    public function getRole(): Role;

    /**
     * Get a user Id.
     *
     * @return int
     */
    public function getId(): int;
}
