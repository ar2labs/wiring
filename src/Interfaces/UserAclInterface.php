<?php

namespace Wiring\Interfaces;

interface UserAclInterface
{
    /**
     * @return \Wiring\Permissions\Acl\Role
     */
    public function getRole();

    /**
     * @return int
     */
    public function getId();
}
