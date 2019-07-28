<?php

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
     * @return Permission
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
