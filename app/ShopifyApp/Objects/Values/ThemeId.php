<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeId as ThemeIdValue;
use Funeralzone\ValueObjects\Scalars\IntegerTrait;

/**
 * Value object for theme's ID.
 */
final class ThemeId implements ThemeIdValue
{
    use IntegerTrait;
}
