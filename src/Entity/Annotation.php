<?php

namespace DevCoder\Entity;

/**
 * Class Annotation
 * @package Webby\Entity
 */
class Annotation
{

    /**
     * @var array
     */
    private static $types = array(
        'bool',
        'boolean',
        'string',
        'int',
        'integer',
        'float',
        'double',
        'array',
        'object',
        'callable',
        'resource',
        'mixed',
        'iterable',
    );

    /**
     * @param string $className
     * @param string $annotation
     * @return array
     * @throws \ReflectionException
     */
    public static function isMapped(string $className, string $annotation = '@Mapped'): array
    {

        $rc = new \ReflectionClass($className);
        $properties = [];
        foreach ($rc->getProperties() as $property) {

            preg_match_all(
                '#@(.*?)\n#s',
                (new \ReflectionProperty($property->class, $property->name))->getDocComment(),
                $annotations
            );
            $annotations[0] = array_map('trim', $annotations[0]);

            if (in_array($annotation, $annotations[0])) {
                $properties[] = $property->name;
            }

        }

        return $properties;

    }

    /**
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    public static function getTypesOfProperties(string $className) : array
    {

        $rc = new \ReflectionClass($className);
        $properties = [];
        foreach ($rc->getProperties() as $property) {
            if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $annotations) == false) {
                continue;
            }
            list(, $type) = $annotations;
            if (in_array($type, self::$types)) {
                $properties[$property->name] = $type;
            }
        }

        return $properties;
    }
}
