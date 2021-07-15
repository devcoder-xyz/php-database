<?php

namespace DevCoder\DB\Entity;

use DevCoder\DB\Helper\EntityHelper;

class EntityFactory
{
    public static function create(string $entityName): EntityInterface
    {
        /**
         * @var EntityInterface $entity
         */
        $entity = EntityHelper::getReflection($entityName)->newInstance();
        return $entity;
    }
}