<?php

namespace DevCoder\DB\Mapping;

class Column implements MappingInterface
{
    /** @var string */
    protected $property;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $type;

    public function __construct(string $property, ?string $name = null, ?string $type = null)
    {
        $this->property = $property;
        $this->name = $name === null ? $property : $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
