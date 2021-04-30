<?php

namespace Fad\Helper;

use Fad\Entity\Annotation;

/**
 * Class Hydrate
 * @package Fad\Helper
 */
class Hydrate
{

    /**
     * @param object $object
     * @param int $id
     * @throws \ReflectionException
     */
    public static function setId(object $object, int $id): void
    {
        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($object, $id);

    }


    /**
     * @param string $className
     * @param array $data
     * @return object
     * @throws \ReflectionException
     */
    public static function lunch(string $className, array $data): object
    {
        $reflection = new \ReflectionClass($className);
        $object = $reflection->newInstanceWithoutConstructor();
        $propertiesTypes = Annotation::getTypesOfProperties($className);
        foreach ($data as $propertyName => $value) {

            if ($propertyName == 'id') {
                self::setId($object, $value);
                continue;
            }

            $propertyName = str_replace('_', '', lcfirst(ucwords($propertyName, '_')));
            if (!$reflection->hasProperty($propertyName)) {
                throw new \InvalidArgumentException("There's no $propertyName property in $className.");
            }

            $property = $reflection->getProperty($propertyName);
            if (isset($propertiesTypes[$propertyName]) && $propertiesTypes[$propertyName] === 'array') {
                $value = json_decode($value, true);
            }elseif (isset($propertiesTypes[$propertyName]) && $propertiesTypes[$propertyName] === 'bool') {
                $value = (bool)$value;
            }

            $method = $reflection->getMethod('set'.ucfirst($propertyName));
            $method->invoke($object, $value);
        }
        return $object;
    }

    /**
     * @param object $object
     * @param $data
     * @return object
     * @throws \ReflectionException
     */
    public static function into(object $object, $data): object
    {
        return self::hydrate(get_class($object), $data);
    }


}