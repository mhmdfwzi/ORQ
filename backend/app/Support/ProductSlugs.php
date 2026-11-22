<?php

namespace App\Support;

final class ProductSlugs
{
    public const LEGACY = [
        'royal-oud' => 'motamared',
        'oud-ambassador' => 'ghoroor',
        'oud-madawi' => 'kaseh',
    ];

    public static function canonical(string $slug): string
    {
        return self::LEGACY[$slug] ?? $slug;
    }
}
