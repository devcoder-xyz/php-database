<?php

namespace Fad\Helper;

/**
 * Class ObjectToArray
 * @package Fad\Helper
 */
class ObjectToArray
{

    /**
     * @param object $object
     * @param bool $saveToDatabase
     * @return array
     * @throws \ReflectionException
     */
    public static function convert(object $object, bool $saveToDatabase = false) : array
    {
        $array = [];
        (new \ReflectionClass($object))->getProperties();
        foreach ((new \ReflectionClass($object))->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if ($saveToDatabase === true) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }elseif($value instanceof \DateTimeInterface) {
                    $value = $value->format('Y-m-d H:i:s');
                }
            }
            $array [$property->getName()] = $value;
        }
        return $array;
    }

}