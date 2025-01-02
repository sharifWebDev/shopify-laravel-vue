<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeRole as ThemeRoleValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for theme's role (nullable).
 */
final class NullableThemeRole extends Nullable implements ThemeRoleValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return ThemeRole::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullThemeRole::class;
    }
}
