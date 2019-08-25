<?php

declare(strict_types=1);

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
    public function __construct(
        ?string $name = null,
        ?string $ownerField = null
    ) {
        $this->name = (string)$name;
        $this->ownerField = (string)$ownerField;
    }

    /**
     * Get a resource.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set a resource.
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
     * Get a owner field.
     *
     * @return string
     */
    public function getOwnerField(): ?string
    {
        return $this->ownerField;
    }

    /**
     * Set a owner field.
     *
     * @param string $ownerField
     *
     * @return self
     */
    public function setOwnerField(string $ownerField): self
    {
        $this->ownerField = $ownerField;

        return $this;
    }
}
