<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeName as ThemeNameValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for theme's name (nullable).
 */
final class NullableThemeName extends Nullable implements ThemeNameValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return ThemeName::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullThemeName::class;
    }
}
