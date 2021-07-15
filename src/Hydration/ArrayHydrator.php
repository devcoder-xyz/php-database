<?php

namespace DevCoder\DB\Hydration;

use DevCoder\DB\Entity\EntityInterface;
use DevCoder\DB\Helper\EntityHelper;
use DevCoder\DB\Mapping\MappingInterface;
use DevCoder\DB\Mapping\OneToOne;
use DevCoder\DB\Mapping\Relation;
use DevCoder\DB\Types\TypeFactory;

class ArrayHydrator extends AbstractHydrator
{
    public function getResult(\PDOStatement $statement): array
    {
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $entities = [];
        foreach ($data as $entityArray) {
            $entities[] = $this->convertToEntityArray($entityArray, $this->reflection);
        }
        return $entities;
    }

    final static protected function convertToEntityArray(array $data, \ReflectionClass $reflectionClass): array
    {
        /**
         * @var MappingInterface $column
         */
        $entityArray = [];
        $repository = $reflectionClass->getMethod('getRepository')->invoke(null);
        $table = call_user_func([$repository, 'getTable']);
        foreach ($reflectionClass->getMethod('getColumns')->invoke(null) as $column) {
            $key = $table . '_' . $column->getName();
            $value = $data[$key] ?? null;
            if ($column instanceof OneToOne && $value !== null && $column->getFetchMode() === 'EAGER') {
                $value = self::convertToEntityArray($data , EntityHelper::getReflection($column->getTargetEntity()));
            }

            $type = $column->getType();
            if ($type !== null) {
                $value = TypeFactory::create($type)->convertToArray($value);
            }

            $entityArray[$column->getProperty()] = $value;
        }

        return $entityArray;
    }
}
