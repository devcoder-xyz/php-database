<?php

namespace DevCoder\DB\Types;

abstract class Type
{
    final public function __construct()
    {
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract public function convertToDatabase($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract public function convertToModel($value);

    public function convertToArray($value)
    {
        return $this->convertToModel($value);
    }
}