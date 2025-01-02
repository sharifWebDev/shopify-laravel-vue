<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeRole as ThemeRoleValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for theme's role (null).
 */
final class NullThemeRole implements ThemeRoleValue
{
    use NullTrait;
}
