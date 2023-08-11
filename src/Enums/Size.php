<?php

namespace Wardenyarn\Loripsum\Enums;

class Size
{
    public const SHORT = 'short';

    public const MEDIUM = 'medium';

    public const LONG = 'long';

    public const VERY_LONG = 'verylong';

    public const AVAILABLE_SIZES = [
        self::SHORT,
        self::MEDIUM,
        self::LONG,
        self::VERY_LONG,
    ];

    public static function isValid(string $size): bool
    {
        return in_array($size, self::AVAILABLE_SIZES);
    }

    public static function getRandom(): string
    {
        return self::AVAILABLE_SIZES[array_rand(self::AVAILABLE_SIZES, 1)];
    }
}
