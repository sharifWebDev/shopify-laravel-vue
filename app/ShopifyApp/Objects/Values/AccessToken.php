<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\AccessToken as AccessTokenValue;
use Funeralzone\ValueObjects\Scalars\StringTrait;

/**
 * Value object for shop's offline access token.
 */
final class AccessToken implements AccessTokenValue
{
    use StringTrait;

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->toNative());
    }
}
