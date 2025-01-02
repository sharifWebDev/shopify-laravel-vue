<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeSupportLevel as ThemeSupportLevelValue;
use Funeralzone\ValueObjects\Scalars\IntegerTrait;

/**
 * Value object for shop's ID.
 */
final class ThemeSupportLevel implements ThemeSupportLevelValue
{
    use IntegerTrait;
}
