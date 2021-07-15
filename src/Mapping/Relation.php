<?php

namespace DevCoder\DB\Mapping;

abstract class Relation extends Column
{
    /** @var string */
    protected $targetEntity;

    /**
     * The fetching strategy to use for the association.
     *
     * @var string
     * ({"EAGER", "EXTRA_LAZY"})
     */
    protected $fetchMode;
    
    public function __construct(string $property, string $name, string $targetEntity, string $fetchMode = 'EAGER')
    {
        parent::__construct($property, $name, null);
        $this->targetEntity = $targetEntity;
        $this->fetchMode = $fetchMode;
    }

    /**
     * @return string
     */
    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }

    /**
     * @return string
     */
    public function getFetchMode(): string
    {
        return $this->fetchMode;
    }
}
