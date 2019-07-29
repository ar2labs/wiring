<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Wiring\Permissions\Acl\Role;

interface UserAclInterface
{
    /**
     * @return Role
     */
    public function getRole(): Role;

    /**
     * @return int
     */
    public function getId(): int;
}
