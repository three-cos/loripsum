<?php

namespace Wardenyarn\Loripsum\Enums;

class Option
{
    public const DECORATE = 'decorate';

    public const LINK = 'link';

    public const UNORDERED_LIST = 'ul';

    public const ORDERED_LIST = 'ol';

    public const DESCRIPTION_LIST = 'dl';

    public const BLOCKQUOTE = 'bq';

    public const CODE = 'code';

    public const HEADERS = 'headers';

    public const AS_PLAINTEXT = 'plaintext';

    public const ALL_CAPS = 'allcaps';

    public const PRUDE = 'prude';

    public const AVAILABLE_OPTIONS = [
        self::DECORATE,
        self::LINK,
        self::UNORDERED_LIST,
        self::ORDERED_LIST,
        self::DESCRIPTION_LIST,
        self::BLOCKQUOTE,
        self::CODE,
        self::HEADERS,
        self::AS_PLAINTEXT,
        self::ALL_CAPS,
        self::PRUDE,
    ];

    public static function isValid(string $option): bool
    {
        return in_array($option, self::AVAILABLE_OPTIONS);
    }

    public static function getRandomOptions(int $count = 1): array
    {
        if ($count === 1) {
            return [
                self::AVAILABLE_OPTIONS[array_rand(self::AVAILABLE_OPTIONS, 1)],
            ];
        }

        if ($count > count(self::AVAILABLE_OPTIONS)) {
            $count = count(self::AVAILABLE_OPTIONS);
        }

        $return = [];
        foreach (array_rand(self::AVAILABLE_OPTIONS, $count) as $option) {
            $return[] = self::AVAILABLE_OPTIONS[$option];
        }

        return $return;
    }
}
