<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ShopId as ShopIdValue;
use Funeralzone\ValueObjects\Scalars\IntegerTrait;

/**
 * Value object for shop's ID.
 */
final class ShopId implements ShopIdValue
{
    use IntegerTrait;
}
