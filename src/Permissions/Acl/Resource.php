<?php

namespace Wiring\Permissions\Acl;

class Resource
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $ownerField;

    /**
     * Resource constructor.
     *
     * @param string|null $name
     * @param string|null $ownerField
     */
    public function __construct($name = null, $ownerField = null)
    {
        $this->name = $name;
        $this->ownerField = $ownerField;
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
     * @return string
     */
    public function getOwnerField()
    {
        return $this->ownerField;
    }

    /**
     * @param string $ownerField
     *
     * @return self
     */
    public function setOwnerField($ownerField)
    {
        $this->ownerField = $ownerField;

        return $this;
    }
}
