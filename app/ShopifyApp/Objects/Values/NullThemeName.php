<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeName as ThemeNameValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for theme's name (null).
 */
final class NullThemeName implements ThemeNameValue
{
    use NullTrait;
}
