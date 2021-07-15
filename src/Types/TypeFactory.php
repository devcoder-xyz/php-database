<?php

namespace DevCoder\DB\Types;

final class TypeFactory
{
    /**
     * @param string $typeName
     * @return Type
     * @throws \ReflectionException
     */
    public static function create(string $typeName): Type
    {
        $type = (new \ReflectionClass($typeName))->newInstance();
        if (!$type instanceof Type) {
            throw new \InvalidArgumentException($typeName. ' must be an instance of '.Type::class);
        }
        return $type;
    }
}
