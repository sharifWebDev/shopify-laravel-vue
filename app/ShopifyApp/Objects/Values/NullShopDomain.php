<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for the shop's domain (null).
 */
final class NullShopDomain implements ShopDomainValue
{
    use NullTrait;
}
