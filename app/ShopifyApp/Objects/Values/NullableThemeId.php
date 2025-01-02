<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeId as ThemeIdValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for theme's ID (nullable).
 */
final class NullableThemeId extends Nullable implements ThemeIdValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return ThemeId::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullThemeId::class;
    }
}
