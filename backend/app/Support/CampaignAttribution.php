<?php

namespace App\Support;

class CampaignAttribution
{
    public const FIELDS = ['utm_source', 'utm_medium', 'utm_campaign'];

    // Keep identifiers stable across checkout, link creation, and spend entry.
    public const PATTERN = '/\A[\p{L}\p{N}][\p{L}\p{M}\p{N} _.-]{0,99}\z/u';

    public static function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    public static function identifier(mixed $value): ?string
    {
        return is_string($value) && preg_match(self::PATTERN, trim($value)) ? self::normalize($value) : null;
    }

    public static function fieldsFrom(array $values): array
    {
        $fields = [];

        foreach (self::FIELDS as $field) {
            $value = self::identifier($values[$field] ?? null);
            if ($value === null) {
                return [];
            }

            $fields[$field] = $value;
        }

        return $fields;
    }
}
