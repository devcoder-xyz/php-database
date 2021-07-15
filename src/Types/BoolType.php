<?php

namespace DevCoder\DB\Types;

final class BoolType extends Type
{

    public function convertToDatabase($value): ?int
    {
        return $value === null ? null : (int)$value;
    }

    public function convertToModel($value): ?bool
    {
        return $value === null ? null : (bool)$value;
    }
}
