<?php

namespace DevCoder\DB\Types;

final class DateTimeType extends Type
{

    public function convertToDatabase($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        throw new \LogicException('Could not convert PHP value "' . $value . '" to ' . self::class);
    }

    public function convertToModel($value): ?\DateTimeInterface
    {
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if (!$date instanceof \DateTimeInterface) {
            throw new \LogicException('Could not convert database value "' . $value . '" to ' . self::class);
        }

        return $date;
    }

    public function convertToArray($value)
    {
        $value = parent::convertToArray($value);
        return $value === null ? null : (array)$value;
    }
}
