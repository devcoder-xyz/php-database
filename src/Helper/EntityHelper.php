<?php

namespace DevCoder\DB\Helper;

use DevCoder\DB\Entity\EntityInterface;
use DevCoder\DB\Mapping\PrimaryKey;
use DevCoder\DB\Types\TypeFactory;

final class EntityHelper
{
    public static function setId(int $id, EntityInterface $entity): void
    {
        foreach ($entity::getColumns() as $column) {
            if ($column instanceof PrimaryKey) {
                $reflection = (new \ReflectionClass($entity));
                $property = $reflection->getProperty($column->getProperty());
                $property->setAccessible(true);
                $property->setValue($entity, $id);
                break;
            }
        }
    }

    public static function extract(EntityInterface $entity): array
    {
        $array = [];
        $reflection = (new \ReflectionClass($entity));
        foreach ($entity::getColumns() as $column) {
            if ($column instanceof PrimaryKey) {
                continue;
            }
            $property = $reflection->getProperty($column->getProperty());
            $property->setAccessible(true);

            $value = $property->getValue($entity);

            $type = $column->getType();
            if ($type !== null) {
                $value = TypeFactory::create($type)->convertToDatabase($value);
            }
            $array[$column->getName()] = $value;
        }
        return $array;
    }

    public static function getReflection(string $entityName): \ReflectionClass
    {
        $reflection = new \ReflectionClass($entityName);
        if (!$reflection->implementsInterface(EntityInterface::class)) {
            throw new \InvalidArgumentException($entityName . ' must be an instance of ' . EntityInterface::class);
        }
        return $reflection;
    }
}
