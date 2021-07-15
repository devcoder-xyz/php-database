<?php

namespace DevCoder\DB\Hydration;

use DevCoder\DB\Helper\EntityHelper;

abstract class AbstractHydrator
{
    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    public function __construct(string $entityName)
    {
        $this->reflection = EntityHelper::getReflection($entityName);
    }

    /**
     * @param \PDOStatement $statement
     * @return mixed
     */
    abstract public function getResult(\PDOStatement $statement);
}
