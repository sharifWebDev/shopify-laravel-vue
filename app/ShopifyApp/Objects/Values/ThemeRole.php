<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeRole as ThemeRoleValue;
use Funeralzone\ValueObjects\Scalars\StringTrait;

/**
 * Value object for theme's role.
 */
final class ThemeRole implements ThemeRoleValue
{
    use StringTrait;
}
