<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for the shop's domain (nullable).
 */
final class NullableShopDomain extends Nullable implements ShopDomainValue
{
    protected $value;
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return ShopDomain::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullShopDomain::class;
    }

    public function getValue()
    {
        return $this->value;
    }
}
