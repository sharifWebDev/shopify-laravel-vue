<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeName as ThemeNameValue;
use Funeralzone\ValueObjects\Scalars\StringTrait;

/**
 * Value object for theme's name.
 */
final class ThemeName implements ThemeNameValue
{
    use StringTrait;
}
