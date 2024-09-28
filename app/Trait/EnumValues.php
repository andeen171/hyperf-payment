<?php

namespace App\Trait;

trait EnumValues
{
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function valuesAsString(): string
    {
        return implode(',', self::values());
    }

}