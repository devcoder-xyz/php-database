<?php

namespace DevCoder\DB\Hydration;

use DevCoder\DB\Entity\EntityFactory;
use DevCoder\DB\Entity\EntityInterface;
use DevCoder\DB\Helper\EntityHelper;
use DevCoder\DB\Mapping\OneToOne;
use DevCoder\DB\Mapping\PrimaryKey;
use DevCoder\DB\Mapping\Relation;
use DevCoder\DB\Types\TypeFactory;

abstract class AbstractObjectHydrator extends AbstractHydrator
{
    final protected function hydrate(array $data): EntityInterface
    {
        return self::hydrateEntity($data, $this->reflection);
    }

    private static function hydrateEntity(array $data, \ReflectionClass $reflectionClass) : EntityInterface
    {
        /**
         * @var EntityInterface $entity
         */
        $entity = $reflectionClass->newInstance();
        $repository = $entity::getRepository();
        $table = call_user_func([$repository, 'getTable']);
        foreach ($entity::getColumns() as $column) {
            $key = $table . '_' . $column->getName();
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            if ($column instanceof OneToOne && $value !== null) {
                switch ($column->getFetchMode()) {
                    case 'EXTRA_LAZY':
                        $id = $value;
                        $value = EntityFactory::create($column->getTargetEntity());
                        EntityHelper::setId($id, $value);
                        break;
                    case 'EAGER':
                        $value = self::hydrateEntity($data , EntityHelper::getReflection($column->getTargetEntity()));
                        break;
                    default:
                        continue 2;
                }
            }

            $type = $column->getType();
            if ($type !== null) {
                $value = TypeFactory::create($type)->convertToModel($value);
            }

            $property = $reflectionClass->getProperty($column->getProperty());
            if ($column instanceof PrimaryKey) {
                $property->setAccessible(true);
                $property->setValue($entity, $value);
                continue;
            }

            if ($property->isPublic()) {
                $property->setValue($entity, $value);
                continue;
            }

            $method = $reflectionClass->getMethod('set' . ucfirst($column->getProperty()));
            $method->invoke($entity, $value);
        }

        return $entity;
    }
}
