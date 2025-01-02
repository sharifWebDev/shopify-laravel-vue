<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeId as ThemeIdValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for theme's ID (null).
 */
final class NullThemeId implements ThemeIdValue
{
    use NullTrait;
}
